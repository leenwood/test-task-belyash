Чтобы запустить проект, для начала надо чтобы все .env.dist, были скопированы в .env.
Композер тоже требуется прописать в папке app (composer install)
По стандарту идет настройка проекта.
```
docker compose build
docker compose up -d
```

___
Решение состоит из двух видов, базоый рекурсивный метод и через итеративный способ.
Все они вызываются через команду
```
bin/console app:execute {pattern} {type}
```
type может быть r - рекурсивный, i - итеративный, по стандарту i.
___
Redis добавил в попытках оптимизировать и ускорить работу, но это уже задачка со звездочкой и в 2 часа не уложиться поэтому базовыми переборами решал.
