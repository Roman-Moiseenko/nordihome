<div class="parser-list-item" style="">
    <div class="parser-item-img">
        <img src="{image}">
    </div>
    <div class="parser-item-data">
        <h4>{name}</h4>
        <div class="description-product">{description}</div>
        <div><span>Артикул: </span><span class="code-selected">{code}</span></div>
        <div><span>Вес: </span><strong>{weight} кг</strong></div>
        <div><span>Кол-во пачек: </span><strong>{pack} шт.</strong></div>
        <div><span>Наличие в ИКЕА: </span></div>
        <div class="parser-item-quantity">{quantity}</div>
        <div class="parser-list-item--bottom">
            <div class="parser-list-item--cost">{cost} ₽</div>
            <div class="parser-list-item--form">
                <button id="delete-button" data-code="{code}"><i class="demo-icon icon-bitbucket"></i></button>
                <button id="decrease-button" data-code="{code}"><i class="demo-icon icon-minus"></i></button>
                <div><div id="count-{code}">{count}</div></div>
                <button id="increase-button" data-code="{code}"><i class="demo-icon icon-plus"></i></button>
            </div>
        </div>
    </div>
</div>
