# 📱 Game Marketplace - Android App

Мобильное приложение для Game Marketplace, созданное с использованием WebView.

## 🚀 Особенности

- **WebView-приложение** - Полная интеграция с веб-версией
- **Pull-to-refresh** - Обновление контента свайпом вниз
- **Адаптивный дизайн** - Оптимизировано для мобильных устройств
- **Навигация** - Поддержка кнопки "Назад"
- **Офлайн-режим** - Кэширование контента

## 📋 Требования

- Android Studio Arctic Fox или новее
- Android SDK 21+ (API Level 21)
- JDK 8 или новее
- Gradle 7.0+

## 🔧 Установка и сборка

### 1. Клонирование репозитория
```bash
git clone https://github.com/your-username/game-marketplace-android.git
cd game-marketplace-android
```

### 2. Настройка URL сервера
Откройте `app/src/main/java/com/gamemarketplace/app/MainActivity.java` и измените URL:
```java
String appUrl = "https://your-domain.com/game_marketplace";
```

### 3. Сборка APK

#### Автоматическая сборка:
```bash
./build_apk.sh
```

#### Ручная сборка через Android Studio:
1. Откройте проект в Android Studio
2. Выберите Build → Build Bundle(s) / APK(s) → Build APK(s)
3. APK будет создан в `app/build/outputs/apk/debug/`

#### Сборка через командную строку:
```bash
./gradlew assembleDebug
```

## 📱 Установка APK

1. Включите "Неизвестные источники" в настройках Android
2. Скачайте APK файл
3. Установите приложение

## 🎨 Настройка

### Изменение иконки приложения:
Замените файлы в `app/src/main/res/mipmap-*/`

### Изменение названия:
Отредактируйте `app/src/main/res/values/strings.xml`

### Изменение цветов:
Отредактируйте `app/src/main/res/values/colors.xml`

## 🔒 Безопасность

- Приложение запрашивает только необходимые разрешения
- Все сетевые запросы выполняются через HTTPS
- WebView настроен с безопасными параметрами

## 📊 Размер APK

- Debug версия: ~5-8 MB
- Release версия: ~3-5 MB (с оптимизацией)

## 🚀 Публикация

### Подготовка к релизу:
1. Измените `versionCode` и `versionName` в `app/build.gradle`
2. Подпишите APK:
```bash
./gradlew assembleRelease
```

### Google Play Store:
1. Создайте аккаунт разработчика
2. Загрузите APK в Google Play Console
3. Заполните описание и скриншоты

## 🐛 Отладка

### Включение отладки WebView:
```java
if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
    WebView.setWebContentsDebuggingEnabled(true);
}
```

### Логи:
```bash
adb logcat | grep "GameMarketplace"
```

## 📞 Поддержка

Если у вас есть вопросы или проблемы:
1. Создайте Issue в репозитории
2. Проверьте логи приложения
3. Убедитесь в правильности URL сервера

---

**Game Marketplace Android** - Мобильная версия для геймеров! 🎮📱