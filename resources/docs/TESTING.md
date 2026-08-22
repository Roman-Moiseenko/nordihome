# Инструкция по написанию unit-тестов для модулей

> Предназначена для AI-агента, который пишет/переписывает тесты для модулей
> в модульном монолите `kadevland/laravel-easy-modules` (Clean Architecture).

## 0. Общий алгоритм (порядок действий)

1. Изучи модуль: `Domain/`, `Application/`, `Infrastructure/`, `Presentation/`.
2. Составь списки «как есть»:
   - Entity — `app/Modules/{Module}/Domain/Entities/*.php`
   - Value Objects — `app/Modules/{Module}/Domain/ValueObjects/*.php`
   - UseCase — `app/Modules/{Module}/Application/Actions/**/*.php`
3. Изучи существующие тесты в `app/Modules/{Module}/Tests/`.
4. Убедись, что тесты модуля подключены к `php artisan test` (см. §1).
5. Удали тесты, чьих сущностей больше нет в модуле.
6. Перепиши тесты под **актуальные** сигнатуры конструкторов/методов.
7. Добавь недостающие тесты для каждой Entity, VO и UseCase.
8. Запусти `php artisan test` и добейся зелёного прогона.

---

## 1. Подключение тестов модуля к `php artisan test`

Тесты модуля лежат внутри самого модуля:
`app/Modules/{Module}/Tests/Unit/...`

Но **по умолчанию PHPUnit их не видит** — `php artisan test` сканирует только
`tests/Unit` и `tests/Feature` из корня. Поэтому в [`phpunit.xml`](../../phpunit.xml)
в оба testsuite добавлены каталоги модулей через wildcard:

```xml
<testsuite name="Unit">
    <directory>tests/Unit</directory>
    <directory>app/Modules/*/Tests/Unit</directory>
</testsuite>
<testsuite name="Feature">
    <directory>tests/Feature</directory>
    <directory>app/Modules/*/Tests/Feature</directory>
</testsuite>
```

**Правило:** если видишь «No tests found» для нового модуля — проверь именно это.
Не создавай копии тестов в корневом `tests/`.

---

## 2. Стек и базовый класс

- Тесты наследуются от `PHPUnit\Framework\TestCase` (НЕ от `Tests\TestCase`).
- Мокинг зависимостей — `Mockery`.
- DTO — `Spatie\LaravelData\Data` (создаются обычным `new Dto(...)` или
  `Dto::validateAndCreate([...])`).
- Namespace теста повторяет путь:
  `App\Modules\{Module}\Tests\Unit\...`

### Структура каталогов тестов

```
app/Modules/{Module}/Tests/
└── Unit/
    ├── Domain/
    │   ├── Entities/        # по одному тесту на Entity
    │   └── ValueObjects/    # по одному тесту на VO
    └── Application/
        └── Actions/         # по одному тесту на UseCase
            └── {Model}/
```

---

## 3. Обязательные поля для тестов UseCase

Почти все UseCase проверяют права через `UserPermission` и бросают
`App\Modules\Shared\Domain\Exceptions\AccessDeniedException`.

Для мока прав используем трейт [`Tests\Trait\MockPermission`](../../tests/Trait/MockPermission.php).

```php
use Tests\Trait\MockPermission;

class ViewStaffUseCaseTest extends TestCase
{
    use MockPermission;

    // Обязательно реализовать оба метода — их использует трейт
    public function getModuleName(): string { return 'auth'; }
    public function getEntityName(): string { return 'employee'; }
}
```

`mockUserPermission()` формирует имена прав как
`{ModuleName}.{EntityName}.{action}` и поддерживает флаги:
`view`, `create`, `edit`, `delete`, `force`, `blocked`, `id`, `role`.

**Право в UseCase должно совпадать** с префиксом из трейта. Например, если
`getModuleName() = 'auth'`, `getEntityName() = 'employee'`, то в UseCase
ожидается `$permissions->can('auth.employee.view')`. Если имена не совпадают —
тест доступа упадёт (или наоборот, не сработает). При расхождении исправь
значения в тесте под фактическое имя права из кода UseCase.

---

## 4. Паттерн теста UseCase

```php
class ViewStaffUseCaseTest extends TestCase
{
    use MockPermission;

    private StaffRepositoryInterface $staffRepo;
    private ViewStaffUseCase $useCase;

    public function getModuleName(): string { return 'auth'; }
    public function getEntityName(): string { return 'employee'; }

    protected function setUp(): void
    {
        parent::setUp();
        $this->staffRepo = Mockery::mock(StaffRepositoryInterface::class);
        $this->useCase = new ViewStaffUseCase($this->staffRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();   // ОБЯЗАТЕЛЬНО
        parent::tearDown();
    }

    public function test_returns_entity(): void
    {
        $entity = new StaffEntity(/* ... */);
        $entity->id = 5;

        $this->staffRepo->shouldReceive('findById')->with(5)->once()->andReturn($entity);

        $permission = $this->mockUserPermission(view: true);
        $this->assertSame($entity, $this->useCase->execute(5, $permission));
    }

    public function test_throws_when_not_found(): void
    {
        $this->staffRepo->shouldReceive('findById')->with(99)->once()->andReturn(null);

        $permission = $this->mockUserPermission(view: true);
        $this->expectException(StaffNotFoundException::class);
        $this->useCase->execute(99, $permission);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(view: false);
        $this->staffRepo->shouldNotReceive('findById');   // репозиторий НЕ вызывается

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(5, $permission);
    }
}
```

**Правила:**
- В `setUp()` создавай UseCase с моками зависимостей вручную (`new UseCase(...)`),
  без Laravel-контейнера.
- `Mockery::close()` в `tearDown()` — всегда.
- Проверяй **все три сценария** как минимум: успех, «не найдено»/ошибка домена,
  «доступ запрещён».
- Если UseCase не проверяет права (например, консольные `RegisterAdminUseCase`)
  — тест на `AccessDeniedException` не нужен.

---

## 5. Тест Entity

- Создай объект через конструктор с обязательными полями.
- Проверь геттеры/хуки, присвоение `id`, методы состояния (`ban()`, `publish()` и т.п.).
- Не трогай Eloquent и БД — только доменный объект.

---

## 6. Тест Value Object

- Проверяй: создание с валидными значениями, нормализацию (lower/trim), `equals()`,
  выброс `InvalidArgumentException` на невалидных значениях.

```php
public function test_creates_valid_email(): void
{
    $email = new Email('Test@Example.com');
    $this->assertSame('test@example.com', $email->value);
}

public function test_throws_on_invalid_format(): void
{
    $this->expectException(InvalidArgumentException::class);
    new Email('invalid');
}
```

---

## 7. DTO с именованными аргументами

Всегда передавай параметры DTO **именованными аргументами** — сигнатуры DTO
могут меняться местами/добавляться, и это защищает от скрытых ошибок:

```php
$dto = new StaffCreateData(
    fullName: 'Иванов Иван',
    positions: [StaffPosition::DRIVER],
);
```

**Не используй** позиционную передачу `new Dto(true, 'email', 'pass', [...])` —
она ломается при изменении порядка полей.

---

## 8. Частые грабли

### 8.1. Конкретный тип возврата репозитория
Если `RepositoryInterface::paginate()` возвращает конкретный
`Illuminate\Pagination\LengthAwarePaginator` (а не контракт
`Illuminate\Contracts\Pagination\LengthAwarePaginator`), мокай конкретный класс:

```php
$paginator = Mockery::mock(LengthAwarePaginator::class);
$this->repo->shouldReceive('paginate')->with(15)->once()->andReturn($paginator);
```

Мок контракта упадёт с `TypeError` (Mockery возвращает мок интерфейса, а метод
требует конкретный класс).

### 8.2. PHP 8.4 deprecation «optional parameter before required»
Если при прогоне видишь deprecation на конструкторе DTO — переставь параметры так,
чтобы обязательные шли до необязательных, либо дай необязательному значение по
умолчанию. Пример фикса: `public array $roleNames = []` вместо `public array $roleNames`.

### 8.3. Фасады (Auth, Password, Hash) и Eloquent-модели
UseCase, которые напрямую используют фасады (`Auth::attempt`, `Password::reset`)
или Eloquent (`Role::query()`), **неудобны** для юнит-теста на чистом
`PHPUnit\Framework\TestCase`. Варианты:
- мокать фасад через `Facade::shouldReceive()` (работает, т.к. `php artisan test`
  поднимает Laravel-приложение и регистрирует фасады);
- либо покрыть только ветку проверки прав (она идёт до обращения к фасаду/БД) —
  тест `AccessDeniedException` без БД.

### 8.4. Сущности, которых больше нет
Если Entity/интерфейс удалён из модуля (например, `FreelanceEntity`), а тест на
него остался — тест не соберётся или упадёт. **Удали** такие тесты. Если UseCase
ещё существует, но ссылается на удалённый интерфейс — это битый production-код,
его тестировать нельзя; пропусти и отметь в отчёте.

---

## 9. Запуск тестов

```bash
# весь прогон (все модули)
php artisan test

# только unit-тесты
php artisan test --testsuite=Unit

# только конкретный файл/метод
php artisan test --filter=ViewStaffUseCaseTest
php artisan test --filter=test_returns_entity
```

Признак успеха: `Tests: N passed (M assertions)` без строк `FAILED`/`DEPR`.

---

## 10. Чек-лист перед сдачей

- [ ] Тесты модуля видны в `php artisan test` (проверен `phpunit.xml`).
- [ ] На каждую Entity, VO и UseCase есть тест (или осознанно пропущен с причиной).
- [ ] Удалены тесты несуществующих сущностей.
- [ ] Сигнатуры тестов соответствуют текущему коду (конструкторы, методы, DTO).
- [ ] DTO создаются именованными аргументами.
- [ ] В каждом UseCase-тесте есть `Mockery::close()` и проверка доступа.
- [ ] `php artisan test` зелёный: `Tests: N passed`.
