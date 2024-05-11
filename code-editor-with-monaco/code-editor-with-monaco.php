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

        wp_add_inline_script('monaco-loader', '
            require.config({ paths: { "vs": "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs" } });
            require(["vs/editor/editor.main"], function() {
                var editor = monaco.editor.create(document.getElementById("editor-container"), {
                    value: document.getElementById("content").value,
                    language: "html",
                    theme: "vs-dark",
                });
                editor.getModel().updateOptions({
                    tabSize: 2,
                    insertSpaces: true,
                    autoClosingBrackets: "always",
                    autoClosingQuotes: "always",
                    autoClosingTags: true // 効いてないぽい
                });
                
                editor.onDidChangeModelContent(function() {
                    document.getElementById("content").value = editor.getValue();
                });

                monaco.languages.registerCompletionItemProvider("html", {
                    provideCompletionItems: () => {
                        var suggestions = [{
                            label: "div",
                            kind: monaco.languages.CompletionItemKind.Snippet,
                            insertText: "<div>\${1}</div>",
                            insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                            documentation: "Inserts a div tag"
                        }];
                        return { suggestions: suggestions };
                    }
                });

                
                editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S, function() {
                    document.querySelector("#publish, #save-post").click(); // 保存ボタンを押す
                });

            });
        ', 'after');

        wp_add_inline_script('monaco-loader', '
        require.config({ paths: { "vs": "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs" } });
        require(["vs/editor/editor.main"], function() {
            var cssEditor = document.getElementById("acf-field_66379ff1bb2ed");
            if (!cssEditor) return; 
            console.log(cssEditor);
            
            var editor = monaco.editor.create(document.getElementById("editor-container-css"), {
                value: cssEditor.value,
                language: "css",
                theme: "vs-dark",
            });
            editor.getModel().updateOptions({
                tabSize: 2,
                insertSpaces: true,
                autoClosingBrackets: "always",
                autoClosingQuotes: "always",
                autoClosingTags: true // 効いてないぽい
            });
            // var editor = monaco.editor.create(container, {
            //     value: cssEditor.value,
            //     language: "css",
            //     theme: "vs-dark"
            // });
            editor.onDidChangeModelContent(function() {
                cssEditor.value = editor.getValue();
            });
            
            editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S, function() {
                document.querySelector("#publish, #save-post").click(); // 保存ボタンを押す
            });

            
            // Alt + Shift + F にフォーマット機能を紐付け
            editor.addCommand(monaco.KeyMod.Alt | monaco.KeyMod.Shift | monaco.KeyCode.KEY_F, function() {
                const formatted = prettier.format(editor.getValue(), {
                    parser: "css",
                    plugins: prettierPlugins,
                    tabWidth: 2,
                    useTabs: false
                });
        
                editor.setValue(formatted);
            });
        
        });
        ', 'after');


        // js editor
        wp_add_inline_script('monaco-loader', '
        require.config({ paths: { "vs": "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs" } });
        require(["vs/editor/editor.main"], function() {
            var footerCodeEditor = document.getElementById("acf-field_663f571173e99");
            if (!footerCodeEditor) return; 
            console.log(footerCodeEditor);
            
            var editor = monaco.editor.create(document.getElementById("editor-container-javascript"), {
                value: footerCodeEditor.value,
                language: "html",
                theme: "vs-dark",
            });
            editor.getModel().updateOptions({
                tabSize: 2,
                insertSpaces: true,
                autoClosingBrackets: "always",
                autoClosingQuotes: "always",
                autoClosingTags: true
            });
    
            editor.onDidChangeModelContent(function() {
                footerCodeEditor.value = editor.getValue();
            });
            
            editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S, function() {
                document.querySelector("#publish, #save-post").click(); // 保存ボタンを押す
            });
        
        });
    ', 'after');

        // Editorのコンテナスタイル
        echo '
        <style>
        #editor-container { height: 500px; margin-bottom: 100px; }
        #editor-container-css { height: 500px; margin-bottom: 100px; }
        #editor-container-javascript { height: 500px; margin-bottom: 100px; }
        // #content {display: none; } 
        // #sample-permalink a {
        //     color: #f0f0f1;
        // }
        // #acf-field_660bd47daa029 {
        //     display: none; 
        // }
        </style>';
    }
}
add_action('admin_enqueue_scripts', 'enqueue_monaco_editor');

function replace_textarea_with_monaco()
{
    echo '
        <div>
            <div id="editor-container"></div>
            <div id="editor-container-css"></div>
            <div id="editor-container-javascript"></div>
        </div>
    ';
}
add_action('edit_form_after_title', 'replace_textarea_with_monaco');
