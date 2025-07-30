# 📱 Touch Sidebar - Документация

## 🎯 Обзор

Современный тач-сайдбар с стеклянным эффектом и поддержкой свайп-жестов для мобильных устройств платформы Radgold.

## ✨ Возможности

### 🔥 **Основные функции:**
- **Свайп-жесты:** Открытие свайпом вправо с края экрана
- **Стеклянный дизайн:** Backdrop blur эффект с градиентами
- **Touch-friendly:** Оптимизирован для сенсорных устройств
- **Адаптивность:** Автоматическое переключение между desktop/mobile
- **Доступность:** Полная поддержка клавиатуры и screen readers
- **Анимации:** Плавные переходы и анимации

### 🎨 **Дизайн особенности:**
- **Цветные иконки:** Каждая категория имеет свой градиентный цвет
- **Floating button:** Пульсирующая кнопка меню с градиентом
- **Glass morphism:** Прозрачный фон с размытием
- **Тач-анимации:** Эффекты при нажатии и hover
- **Dark mode:** Полная поддержка темной темы

## 📂 Структура файлов

```
resources/
├── views/frontend/layouts/partials/
│   └── dashboard-sidebar.blade.php    # Основной файл сайдбара
├── css/
│   └── app.css                        # Стили для тач-меню
└── js/
    └── touch-sidebar.js               # JavaScript функциональность
```

## 🚀 Использование

### **Включение в макет:**
```blade
@include('frontend.layouts.partials.dashboard-sidebar')
```

### **Подключение ресурсов:**
```blade
@vite(['resources/css/app.css', 'resources/js/touch-sidebar.js'])
```

## 📱 Тач-жесты

### **Открытие сайдбара:**
- **Свайп вправо** с левого края экрана (20px от края)
- **Нажатие** на floating button
- **Быстрый свайп** (по скорости движения)

### **Закрытие сайдбара:**
- **Свайп влево** внутри сайдбара
- **Нажатие** на overlay
- **Кнопка закрытия** (X)
- **Клавиша Escape**

## 🎨 Стилизация

### **CSS классы:**
```css
.glass-sidebar          /* Основной контейнер с glass эффектом */
.touch-menu-item        /* Элемент меню */
.touch-menu-active      /* Активный элемент */
.touch-menu-icon        /* Иконка с градиентом */
.touch-menu-text        /* Текст элемента */
.touch-menu-arrow       /* Стрелка справа */
.floating-menu-btn      /* Плавающая кнопка */
```

### **Цвета иконок по категориям:**
```css
Overview:           blue-500 → blue-600
Public Profile:     green-500 → green-600  
Post Listing:       purple-500 → purple-600
My Ads:             orange-500 → orange-600
Resubmission:       yellow-500 → yellow-600
Favorites:          red-500 → pink-600
Messages:           indigo-500 → indigo-600
Plans & Billing:    emerald-500 → emerald-600
Blocked Users:      gray-500 → gray-600
Affiliate:          cyan-500 → cyan-600
Verify Account:     teal-500 → teal-600
Settings:           slate-500 → slate-600
Logout:             red-500 → red-600
```

## 🔧 Настройка

### **JavaScript параметры:**
```javascript
const touchSidebar = new TouchSidebar();

// Пороги чувствительности
EDGE_THRESHOLD: 20      // Пиксели от края для активации
SWIPE_THRESHOLD: 50     // Минимальное расстояние свайпа
VELOCITY_THRESHOLD: 0.3 // Минимальная скорость для быстрого свайпа
```

### **Alpine.js интеграция:**
```javascript
// Состояние сайдбара
sidebarOpen: false

// Методы
openSidebar()    // Открыть
closeSidebar()   // Закрыть
toggle()         // Переключить
```

## 📊 Анимации

### **Типы анимаций:**
- **Slide in/out:** Плавное появление слева
- **Fade overlay:** Затемнение фона
- **Scale effects:** Масштабирование при нажатии
- **Pulse button:** Пульсация floating кнопки
- **Arrow movement:** Движение стрелок при hover

### **CSS переходы:**
```css
transition-all duration-300 ease-out     /* Основные элементы */
transition-transform duration-200        /* Стрелки */
backdrop-blur-2xl                        /* Размытие фона */
```

## 🌐 Адаптивность

### **Breakpoints:**
- **Desktop (>= 1024px):** Стандартный сайдбар
- **Mobile (< 1024px):** Тач-сайдбар с floating button

### **Автоматическое переключение:**
```javascript
// При изменении размера окна
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024 && touchSidebar) {
        touchSidebar.destroy();
    }
});
```

## ♿ Доступность

### **Keyboard navigation:**
- **Tab:** Навигация по элементам
- **Escape:** Закрытие сайдбара
- **Enter/Space:** Активация элементов

### **Screen readers:**
- Правильные ARIA атрибуты
- Семантически корректная разметка
- Focus trap внутри сайдбара

### **Поддержка настроек:**
```css
@media (prefers-reduced-motion: reduce) {
    /* Отключение анимаций */
}

@media (prefers-contrast: high) {
    /* Повышенный контраст */
}
```

## 📈 Аналитика

### **Отслеживаемые события:**
```javascript
// Google Analytics
gtag('event', 'sidebar_opened', {
    'event_category': 'navigation',
    'event_label': 'swipe'
});

// Custom analytics
window.analytics.track('sidebar_opened', {
    method: 'swipe',
    timestamp: Date.now()
});
```

## 🛠️ API

### **Публичные методы:**
```javascript
// Глобальный доступ
window.touchSidebar.toggle()      // Переключить
window.touchSidebar.openSidebar()  // Открыть
window.touchSidebar.closeSidebar() // Закрыть
window.touchSidebar.destroy()      // Уничтожить
```

### **События:**
```javascript
// Кастомные события
document.addEventListener('sidebar:opened', callback);
document.addEventListener('sidebar:closed', callback);
```

## 🔍 Отладка

### **Console логи:**
```javascript
// Включить отладку
localStorage.setItem('touch_sidebar_debug', 'true');

// Просмотр состояния
console.log(window.touchSidebar.isOpen);
console.log(window.touchSidebar.velocity);
```

### **Визуальная отладка:**
```css
/* Показать touch zones */
.debug .edge-zone {
    position: fixed;
    left: 0;
    top: 0;
    width: 20px;
    height: 100vh;
    background: rgba(255, 0, 0, 0.3);
    pointer-events: none;
}
```

## 🎯 Производительность

### **Оптимизации:**
- Passive touch listeners где возможно
- RequestAnimationFrame для анимаций
- Debounced resize handlers
- Lazy loading иконок

### **Метрики:**
- Время открытия: ~300ms
- Размер JS: ~8KB (минифицирован)
- Размер CSS: ~4KB (после Tailwind purge)

## 🔄 Обновления

### **v1.0.0 (Current):**
- ✅ Базовая функциональность
- ✅ Свайп-жесты  
- ✅ Стеклянный дизайн
- ✅ Доступность
- ✅ Анимации

### **Планируется v1.1.0:**
- [ ] Кастомизация цветов
- [ ] Дополнительные жесты
- [ ] Haptic feedback
- [ ] PWA интеграция

---

## 📞 Поддержка

Если у вас есть вопросы или предложения по улучшению тач-сайдбара, создайте issue в репозитории проекта.

**🎉 Наслаждайтесь современным тач-интерфейсом!**