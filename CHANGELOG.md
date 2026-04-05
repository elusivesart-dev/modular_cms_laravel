# История на промените

Всички значими промени по този проект се документират в този файл.

---

## [1.0.0] - 2026-03-30

### Добавено
- Начално ядро на модулната CMS система (Laravel 12, PHP 8.2)
- Модулна система с регистър, loader и lifecycle
- RBAC система (роли, права, присвояване)
- Интеграция на event система
- Ядро за локализация (езици, смяна, регистър)
- Система за теми (админ + публична част)
- Settings модул с групирани настройки
- Audit лог модул със следене на събития
- Потребителски модул (регистрация, вход, профил, верификация)

### Добавено (Админ табло)
- Админ дашборд
- Статистически карти:
  - Брой модули
  - Брой потребители
  - Брой теми
  - Брой езици
- Последни действия (интеграция с Audit Log)
- Кликаеми карти с навигация

### Променено
- Админ layout-а е преработен с динамичен потребител
- Подобрен language switcher (UI + размер)
- Навигацията е съобразена с RBAC
- Settings UI интегриран с Localization

### Поправено
- Аватари (storage + fallback)
- Връзка actor в audit log
- Проблеми с language dropdown
- Роутинг проблеми
- Премахнати хардкод текстове → localization

### Сигурност
- CSRF защита
- RBAC проверки в навигация и рутове
- Валидация по Laravel стандарт

---

## [Предстоящи]

### Планирани
- Модул за управление на навигация
- Медия модул (централизирани файлове)
- Модул за страници
- Лицензиране (домейн + ключ)
- Marketplace за модули
- Разширяване на API
- Оптимизации на производителността

## [1.0.1] - 2026-04-05
- Медия модул (централизирани файлове) - изпълнен

---

---

# Changelog

All notable changes to this project will be documented in this file.

---

## [1.0.0] - 2026-03-30

### Added
- Initial modular CMS core (Laravel 12, PHP 8.2)
- Module system with registry, loader and lifecycle
- RBAC system (roles, permissions, assignments)
- Event system integration
- Localization core (languages, switching, registry)
- Theme system (admin + public themes)
- Settings module with grouped configuration
- Audit log module with event tracking
- User module (registration, auth, profile, email verification)

### Added (Admin Dashboard)
- Dashboard page (admin)
- Statistics cards:
  - Modules count
  - Users count
  - Themes count
  - Languages count
- Recent activity widget (Audit Log integration)
- Clickable cards with navigation

### Changed
- Admin layout refactored to dynamic user context
- Language switcher improved (UI + dropdown sizing)
- Navigation aligned with RBAC permissions
- Settings UI integrated with Localization

### Fixed
- Avatar rendering (storage + fallback support)
- Audit log actor resolution
- Language dropdown UI issues
- Route inconsistencies
- Hardcoded strings replaced with localization

### Security
- CSRF protection enforced
- RBAC permission checks applied in navigation and routes
- Input validation aligned with Laravel standards

---

## [Unreleased]

### Planned
- Navigation Manager module
- Media module (centralized uploads)
- Pages module (content engine)
- Licensing system (domain + key validation)
- Marketplace for modules
- API system expansion
- Performance optimizations

## [1.0.1] - 2026-04-05
- Media module (centralized uploads) - Done!
---

