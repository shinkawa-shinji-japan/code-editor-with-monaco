<?php
/*
Plugin Name: Code Editor with monaco
Plugin URI: http://yourwebsite.com
Description: Integrates Prettier formatter into the post editor via CDN.
Version: 1.0
Author: Your Name
Author URI: http://yourwebsite.com
*/
function enqueue_prettier_script($hook)
{
    // このスクリプトを投稿ページにのみ追加
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }

    wp_enqueue_script('prettier-standalone', 'https://cdn.jsdelivr.net/npm/prettier@2.3.2/standalone.js');
    wp_enqueue_script('prettier-parser-html', 'https://cdn.jsdelivr.net/npm/prettier@2.3.2/parser-html.js');
    wp_enqueue_script('prettier-parser-postcss', 'https://unpkg.com/prettier@2.7.1/parser-postcss.js');
}
add_action('admin_enqueue_scripts', 'enqueue_prettier_script');


function enqueue_monaco_editor()
{
    $screen = get_current_screen();
    if ('post' === $screen->base) {
        // EditorのCSS
        wp_enqueue_style('monaco-editor-css', 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs/editor/editor.main.css');

        // EditorのJSと設定
        wp_enqueue_script('monaco-loader', 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs/loader.js', array(), null, true);

        // CSS ファイルをエンキュー
        wp_enqueue_style('my-plugin-styles', plugin_dir_url(__FILE__) . 'style.css');

        // JavaScript ファイルをエンキュー
        wp_enqueue_script('my-plugin-scripts', plugin_dir_url(__FILE__) . 'script.js', array(), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_monaco_editor');

function replace_textarea_with_monaco()
{
    echo <<<HTML
    <div class="pTab">
        <div class="pTab__columnsSelector">
            <div class="pTab__columnsSelectorTitle">Columns: </div>
            <div class="pTab__columnChangeButton active" data-columns="1">1</div>
            <div class="pTab__columnChangeButton" data-columns="2">2</div>
            <div class="pTab__columnChangeButton" data-columns="3">3</div>
        </div>
        <div class="pTab__tabNamesContainer">
            <div class="pTab__tabName active" data-tab-name="content">content</div>
            <div class="pTab__tabName" data-tab-name="css">css</div>
            <div class="pTab__tabName" data-tab-name="footer">footer</div>
        </div>
        <div class="pTab__tabItemsContainer">
            <div class="pTab__tabItem active" data-tab-item="content">
                <div id="editor-container" class="editor-container mb100"></div>
            </div>
            <div class="pTab__tabItem" data-tab-item="css">
                    <div id="editor-container-css" class="editor-container mb100"></div>
            </div>
            <div class="pTab__tabItem" data-tab-item="footer">
                    <div id="editor-container-footer" class="editor-container"></div>
            </div>
        </div>
    </div>
    
    <script>
        function updateActiveClasses() {
            if (window.innerWidth >= 1920) {
                document.querySelectorAll(".pTab__tabItem").forEach(item => {
                    item.classList.add("active");
                });
                document.querySelector(".pTab__tabItemsContainer").classList.add("pTab__tabItemsContainer--flex");
            } else {
                document.querySelector(".pTab__tabItemsContainer").classList.remove("pTab__tabItemsContainer--flex");
                document.querySelectorAll(".pTab__tabItem:not(:first-child)").forEach(item => {
                    item.classList.remove("active");
                });
            }
            resetContentEditor()
            resetCssEditor();
            resetFooterEditor();
        }
        window.addEventListener("resize", updateActiveClasses);
        document.addEventListener("DOMContentLoaded", updateActiveClasses);
    </script>
    
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const buttons = document.querySelectorAll(".pTab__columnChangeButton");
        const container = document.querySelector(".pTab__tabItemsContainer");
    
        buttons.forEach(button => {
            button.addEventListener("click", function () {
                // 全てのボタンから "active" クラスを削除
                buttons.forEach(btn => btn.classList.remove("active"));
    
                // クリックされたボタンに "active" クラスを付与
                button.classList.add("active");
    
                // container からすべてのカラムクラスを削除
                container.className = "pTab__tabItemsContainer"; // 元のクラス名にリセット
    
                // クリックされたボタンに応じたクラスをcontainerに追加
                const columnClass = "pTab__tabItemsContainer--col" + button.dataset.columns;
                container.classList.add(columnClass);

                resetContentEditor()
                resetCssEditor();
                resetFooterEditor();
            });
        });
    });
    </script>
    HTML;
}
add_action('edit_form_after_title', 'replace_textarea_with_monaco');
