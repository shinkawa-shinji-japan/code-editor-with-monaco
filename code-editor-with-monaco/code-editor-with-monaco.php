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
        <div class="pTab__toggleFieldsSelector">
            <div class="pTab__toggleFieldsTitle">Show Fields: </div>
            <div class="pTab__toggleFieldsButton active js-toggleButton" data-toggle-fields="content" data-target-editor-id="editor-content" data-target-textarea-id="content" data-target-editor-id="editor-content" data-editor-language="html">Content</div>
        </div>
        <div class="pTab__tabItemsContainer pTab__tabItemsContainer--col3">
            <div class="pTab__tabItem active" data-tab-item="content" id="editor-content">
                <!-- <div id="editor-container" class="editor-container mb100"></div> -->
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const buttons = document.querySelectorAll(".pTab__columnChangeButton");
        const container = document.querySelector(".pTab__tabItemsContainer");
    
        buttons.forEach(button => {
            button.addEventListener("click", function () {
                buttons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");
                container.className = "pTab__tabItemsContainer"; // 元のクラス名にリセット
                const columnClass = "pTab__tabItemsContainer--col" + button.dataset.columns;
                container.classList.add(columnClass);

                {
                    document.querySelectorAll(".pTab__tabItem").forEach(element => {
                        element.classList.remove("active");
                    });
                    let num = 1;
                    document.querySelectorAll(".pTab__tabItem").forEach(element => {
                        if (num <= button.dataset.columns) {
                            element.classList.add("active");
                            num += 1;
                        }
                    });
                }   
                {
                    // TODO: 2を押したときにすでにactiveが2個あるなら以下の処理は行わないようにする
                    // document.querySelectorAll(".pTab__toggleFieldsButton").forEach(element => {
                    //     element.classList.remove("active");
                    // });
                    // let num = 1;
                    // document.querySelectorAll(".pTab__toggleFieldsButton").forEach(element => {
                    //     if (num <= button.dataset.columns) {
                    //         element.classList.add("active");
                    //         num += 1;
                    //     }
                    // });
                }   

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
