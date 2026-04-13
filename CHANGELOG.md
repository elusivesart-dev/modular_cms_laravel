# История на промените

Всички значими промени по проекта се документират в този файл.

Форматът следва семпла и ясна структура по версии:
- Добавено
- Променено
- Поправено
- Сигурност

---

## [1.2.0] - 2026-04-13

### Добавено
- Core Contracts слой за стабилизиране на комуникацията между Auth, Users и RBAC
- Централизиран transaction management през Core database abstraction
- User entity boundary чрез `UserEntityInterface`
- Workflow слой за потребители:
  - `UserAdministrationWorkflowService`
  - `UserProfileWorkflowService`
- Workflow слой за роли:
  - `RoleAdministrationWorkflowService`
- Локализационни ключове за Core module boot/manifest грешки

### Променено
- Премахнати директни зависимости между критични модули в архитектурата
- Users модулът е приведен към contract-driven и workflow-driven data flow
- Roles модулът е приведен към thin controller + workflow orchestration модел
- Runtime settings pipeline е разделен по отговорности:
  - system apply на boot ниво
  - locale apply на request ниво
- Core module manifest discovery/load pipeline е hardened и валидиран
- Controller orchestration е изнесена от:
  - `UserController`
  - `RoleController`
- Application и Domain contracts в Users вече не сочат директно към infrastructure user model
- RBAC bridge flow е стабилизиран върху Core contracts

### Поправено
- Проблеми с boot процеса при липсващи или невалидни module manifest файлове
- Проблеми със settings runtime apply в тестова и boot среда
- Проблеми с languages/settings boot pipeline в тестова среда
- Конфликти с Laravel `MustVerifyEmail` signatures при boundary refactor
- Проблеми с route availability и 404/500 сценарии в тестова среда
- Проблеми с SQLite test execution при migrations и foreign key flow
- Остатъчна orchestration логика в Users и Roles HTTP слоя

### Сигурност
- Подсилена изолация между модулите чрез contract-based boundaries
- Ограничен достъп на application слоя до infrastructure конкретика
- Подобрена консистентност при role/permission synchronization
- По-устойчив и fail-fast Core boot pipeline при невалидна module конфигурация

---

## [1.0.1] - 2026-04-05

### Добавено
- Медия модул за централизирано управление на файлове

### Променено
- Актуализирана проектна история с отразяване на завършения Медия модул

---

## [1.0.0] - 2026-03-30

### Добавено
- Начално ядро на модулната CMS система върху Laravel 12 и PHP 8.2
- Модулна система с регистър, loader и lifecycle управление
- RBAC система за роли, права и присвоявания
- Интеграция на event система
- Ядро за локализация с езици, смяна на език и регистър
- Система за теми за админ и публична част
- Settings модул с групирани настройки
- Audit Log модул със следене на събития
- Потребителски модул с регистрация, вход, профил и имейл верификация

### Добавено (Админ табло)
- Админ дашборд
- Статистически карти за:
  - брой модули
  - брой потребители
  - брой теми
  - брой езици
- Секция за последни действия с интеграция към Audit Log
- Кликаеми карти с навигация

### Променено
- Преработен админ layout с динамичен потребителски контекст
- Подобрен language switcher в интерфейса
- Навигацията е съобразена с RBAC
- Settings UI е интегриран с Localization

### Поправено
- Аватари: storage интеграция и fallback поведение
- Връзка `actor` в Audit Log
- Проблеми с language dropdown
- Роутинг несъответствия
- Премахнати хардкод текстове чрез localization

### Сигурност
- CSRF защита
- RBAC проверки в навигация и рутове
- Валидация по Laravel стандарт

---

## [Предстоящи]

### Планирани
- Модул за управление на навигация
- Медия модул: допълнително разширяване
- Модул за страници
- Лицензираща система с домейн и ключ
- Marketplace за модули
- Разширяване на API слоя
- Оптимизации на производителността