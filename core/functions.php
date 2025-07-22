<?php
// Загальні функції сайту

function site_url($path = '') {
    return '/' . ltrim($path, '/');
}