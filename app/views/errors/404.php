<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/10 to-secondary/10 px-4">
    <div class="max-w-2xl w-full text-center">
        <!-- Анимированная иллюстрация -->
        <div class="mb-8">
            <div class="inline-block relative">
                <div class="w-32 h-32 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-6xl font-bold text-white mb-4 mx-auto shadow-xl animate-bounce">
                    404
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-xl animate-pulse">
                    !
                </div>
            </div>
        </div>

        <!-- Заголовок -->
        <h1 class="text-4xl md:text-6xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-4">
            Страница не найдена
        </h1>

        <!-- Описание -->
        <p class="text-xl text-muted-foreground mb-8 leading-relaxed">
            К сожалению, запрашиваемая страница не существует или была перемещена.<br>
            Возможно, вы перешли по устаревшей ссылке или ввели неверный адрес.
        </p>

        <!-- Кнопки действий -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
            <a href="/" class="btn-primary group">
                <i class="icon-home mr-2"></i>
                Главная страница
                <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>
            
            <button onclick="history.back()" class="btn-secondary">
                <span class="mr-2">←</span>
                Вернуться назад
            </button>
            
            <a href="/catalog" class="btn-secondary">
                <i class="icon-grid mr-2"></i>
                Каталог товаров
            </a>
        </div>

        <!-- Поиск -->
        <div class="bg-card p-6 rounded-2xl shadow-lg border border-border max-w-md mx-auto mb-8">
            <h3 class="text-lg font-semibold mb-4">Попробуйте найти то, что искали</h3>
            <form onsubmit="return searchRedirect(event)" class="space-y-4">
                <div class="relative">
                    <input 
                        type="text" 
                        id="search-input"
                        class="input-field pl-10 pr-12" 
                        placeholder="Поиск товаров..."
                        autocomplete="off"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="icon-search w-5 h-5 text-muted-foreground"></i>
                    </div>
                    <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="icon-arrow-right w-5 h-5 text-primary hover:text-primary/80 transition-colors"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Популярные категории -->
        <div class="text-center">
            <h3 class="text-lg font-semibold mb-4 text-muted-foreground">Популярные категории</h3>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="/catalog?game=valorant" class="inline-block px-4 py-2 bg-accent rounded-lg hover:bg-primary hover:text-white transition-all text-sm">
                    Valorant
                </a>
                <a href="/catalog?game=csgo" class="inline-block px-4 py-2 bg-accent rounded-lg hover:bg-primary hover:text-white transition-all text-sm">
                    CS:GO
                </a>
                <a href="/catalog?game=dota2" class="inline-block px-4 py-2 bg-accent rounded-lg hover:bg-primary hover:text-white transition-all text-sm">
                    Dota 2
                </a>
                <a href="/catalog?game=wow" class="inline-block px-4 py-2 bg-accent rounded-lg hover:bg-primary hover:text-white transition-all text-sm">
                    World of Warcraft
                </a>
                <a href="/catalog?type=boost" class="inline-block px-4 py-2 bg-accent rounded-lg hover:bg-primary hover:text-white transition-all text-sm">
                    Бустинг
                </a>
                <a href="/catalog?type=account" class="inline-block px-4 py-2 bg-accent rounded-lg hover:bg-primary hover:text-white transition-all text-sm">
                    Аккаунты
                </a>
            </div>
        </div>

        <!-- Декоративные элементы -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-primary/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 right-10 w-32 h-32 bg-secondary/10 rounded-full blur-xl"></div>
        <div class="absolute top-1/3 right-20 w-16 h-16 bg-primary/5 rounded-full blur-lg"></div>
    </div>
</div>

<script>
function searchRedirect(event) {
    event.preventDefault();
    const query = document.getElementById('search-input').value.trim();
    if (query) {
        window.location.href = '/catalog?search=' + encodeURIComponent(query);
    }
    return false;
}

// Добавляем иконки поиска и стрелки
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .icon-search::before { content: "🔍"; }
        .icon-arrow-right::before { content: "→"; }
        
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0,-30px,0);
            }
            70% {
                transform: translate3d(0,-15px,0);
            }
            90% {
                transform: translate3d(0,-4px,0);
            }
        }
        
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>

<?php
$content = ob_get_clean();
$title = 'Страница не найдена';
require_once __DIR__ . '/../layouts/main.php';
?>