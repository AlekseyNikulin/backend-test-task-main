create table if not exists products
(
    id int auto_increment primary key,
    uuid varchar(36) not null comment 'UUID товара',
    category varchar(255) not null comment 'Категория товара',
    is_active tinyint default 1 not null comment 'Флаг активности',
    name text default '' not null comment 'Тип услуги',
    description text null comment 'Описание товара',
    thumbnail varchar(255) null comment 'Ссылка на картинку',
    price int not null comment 'Цена в копейках'
)
    comment 'Товары';

create index is_category_active_idx on products (category, is_active);
