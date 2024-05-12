
const initContentEditor = () => {
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
}
initContentEditor();


const initCssEditor = () => {
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
}
initCssEditor();


const initFooterEditor = () => {
    require.config({ paths: { "vs": "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs" } });
    require(["vs/editor/editor.main"], function() {
        var footerCodeEditor = document.getElementById("acf-field_663f571173e99");
        if (!footerCodeEditor) return; 
        console.log(footerCodeEditor);
        
        var editor = monaco.editor.create(document.getElementById("editor-container-footer"), {
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
}
initFooterEditor();


document.querySelectorAll(".pTab__tabName").forEach(element => {
    element.addEventListener("click", function() {
        showTab(this.getAttribute("data-tab-name"), this);
    });
});


const resetContentEditor = () => {
    const tabItem = document.querySelector('[data-tab-item="content"]');

    if (tabItem) {
        tabItem.innerHTML = '<div id="editor-container" class="editor-container"></div>'; 
    }

    initContentEditor();
}
const resetCssEditor = () => {
    const tabItem = document.querySelector('[data-tab-item="css"]');

    if (tabItem) {
        tabItem.innerHTML = '<div id="editor-container-css" class="editor-container"></div>'; 
    }

    initCssEditor();
}
const resetFooterEditor = () => {
    const tabItem = document.querySelector('[data-tab-item="footer"]');

    if (tabItem) {
        tabItem.innerHTML = '<div id="editor-container-footer" class="editor-container"></div>'; 
    }

    initFooterEditor();
}

const showTab = (name, clickedElment) => {
    
    const isAllActive = () => {
        const all = document.querySelectorAll(".pTab__tabItem");
        const len = all.length;
        let i = 0;
        all.forEach(item => {
            if (item.classList.contains("active")) {
                i += 1;
            }
        });
        console.log(i);
        if (i === len) return true;
        return false;
    }
    if (isAllActive()) return;
    
    document.querySelectorAll(".pTab__tabName").forEach(element => {
        element.classList.remove("active");
    });

    document.querySelectorAll(".pTab__tabItem").forEach(element => {
        element.classList.remove("active");
    });

    clickedElment.classList.add("active");
    document.querySelector('[data-tab-item="' + name + '"]').classList.add("active");

    if (name === "content") {
        resetContentEditor()
        return
    }

    if (name === "css") {
        resetCssEditor();
        return
    }

    if (name === "footer") {
        resetFooterEditor();
        return
    }
}