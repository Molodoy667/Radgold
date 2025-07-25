#!/bin/bash

echo "🎮 Сборка APK для Game Marketplace..."

# Проверяем наличие Android SDK
if [ -z "$ANDROID_HOME" ]; then
    echo "❌ ANDROID_HOME не установлен. Установите Android SDK."
    exit 1
fi

# Очищаем предыдущую сборку
echo "🧹 Очистка предыдущей сборки..."
./gradlew clean

# Собираем debug APK
echo "🔨 Сборка debug APK..."
./gradlew assembleDebug

# Проверяем успешность сборки
if [ $? -eq 0 ]; then
    echo "✅ APK успешно собран!"
    echo "📱 Файл: app/build/outputs/apk/debug/app-debug.apk"
    
    # Показываем размер APK
    APK_SIZE=$(du -h app/build/outputs/apk/debug/app-debug.apk | cut -f1)
    echo "📊 Размер APK: $APK_SIZE"
    
    # Копируем APK в корень проекта
    cp app/build/outputs/apk/debug/app-debug.apk ./game-marketplace.apk
    echo "📦 APK скопирован как: game-marketplace.apk"
    
else
    echo "❌ Ошибка при сборке APK"
    exit 1
fi

echo "🎉 Сборка завершена!"