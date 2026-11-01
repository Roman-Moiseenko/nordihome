export const SOURCE_LABELS: Record<string, string> = {
    direct: 'Прямой заход',
    google: 'Google',
    yandex: 'Яндекс',
    yandex_ads: 'Реклама Яндекса (Директ)',
    yandex_ritm: 'Яндекс Ритм',
    bing: 'Bing',
    'mail.ru': 'Mail.ru',
    duckduckgo: 'DuckDuckGo',
    vk: 'ВКонтакте',
    telegram: 'Telegram',
    ok: 'Одноклассники',
    facebook: 'Facebook',
    instagram: 'Instagram',
    utm: 'Рекламные кампании (UTM)',
    internal: 'Внутренние переходы',
    other: 'Другие',
}

export function sourceLabel(value?: string | null): string {
    if (!value) return '—'
    return SOURCE_LABELS[value] ?? value
}
