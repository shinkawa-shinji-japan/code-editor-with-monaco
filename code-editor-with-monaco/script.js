const appendItemDivToItemsContainer = (id) => {
    const newDiv = document.createElement("div");
    newDiv.classList.add("pTab__tabItem");
    newDiv.classList.add("active");
    newDiv.id = id;
    const container = document.querySelector(".pTab__tabItemsContainer");
    container.appendChild(newDiv);

    return newDiv;
};

const createFieldButtons = (
    {
        text,
        editorId,
        initEditor,
        textareaId,
        language
    }
) => {
    const newDiv = document.createElement("div");
    newDiv.classList.add("pTab__toggleFieldsButton");
    newDiv.classList.add("js-toggleButton");
    newDiv.classList.add("active");
    newDiv.innerText = text;
    // newDiv.setAttribute("data-text", text);
    newDiv.setAttribute("data-target-editor-id", editorId);
    newDiv.setAttribute("data-target-textarea-id", textareaId);
    newDiv.setAttribute("data-editor-language", language);

    const container = document.querySelector(".pTab__toggleFieldsSelector");
    container.appendChild(newDiv);
    // divにクリックイベントリスナーを追加
    newDiv.addEventListener("click", () => {
        const tabItem = document.getElementById(editorId);

        if (tabItem.classList.contains("active")) {
            newDiv.classList.remove("active");
            tabItem.classList.remove("active");
            // tabItem.innerHTML = '<div class="editor-container"></div>';
            tabItem.innerHTML = null;
            return;
        }

        tabItem.classList.add("active");
        newDiv.classList.add("active");
        // tabItem.innerHTML = '<div class="editor-container"></div>';
        // initEditor();
        initMonacoEditor({ targetId: editorId, textareaId, language })
    });


    return newDiv;
};

const initMonacoEditor = ({ targetId, textareaId, language }) => {
    const editor = monaco.editor.create(document.getElementById(targetId), {
        value: document.getElementById(textareaId).value,
        language: language,
        theme: "vs-dark",
    });

    editor.getModel().updateOptions({
        tabSize: 2,
        insertSpaces: true,
        autoClosingBrackets: "always",
        autoClosingQuotes: "always",
        autoClosingTags: true, // 効いてないぽい
    });
    editor.onDidChangeModelContent(function () {
        editor.value = editor.getValue();
    });

    editor.addCommand(
        monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S,
        function () {
            document.querySelector("#publish, #save-post").click(); // 保存ボタンを押す
        }
    );

    // Alt + Shift + F にフォーマット機能を紐付け
    editor.addCommand(
        monaco.KeyMod.Alt | monaco.KeyMod.Shift | monaco.KeyCode.KEY_F,
        function () {
            const formatted = prettier.format(editor.getValue(), {
                parser: language,
                plugins: prettierPlugins,
                tabWidth: 2,
                useTabs: false,
            });

            editor.setValue(formatted);
        }
    );
}

const initMonacoEditors = () => {
    require.config({
        paths: {
            vs: "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.21.2/min/vs",
        },
    });
    require(["vs/editor/editor.main"], function () {
        // WordPress content Editor
        {
            const editorId = "editor-content";
            initMonacoEditor({ targetId: editorId, textareaId: "content", language: "html" })
            document.querySelector(".js-toggleButton[data-toggle-fields=\"content\"]").addEventListener("click", function () {
                const newDiv = this;

                const tabItem = document.getElementById(editorId);

                if (tabItem.classList.contains("active")) {
                    newDiv.classList.remove("active");
                    tabItem.classList.remove("active");
                    // tabItem.innerHTML = '<div class="editor-container"></div>';
                    tabItem.innerHTML = null;
                    return;
                }

                tabItem.classList.add("active");
                newDiv.classList.add("active");
                // tabItem.innerHTML = '<div class="editor-container"></div>';
                initMonacoEditor({ targetId: editorId, textareaId: "content", language: "html" })
            });
        }

        let idNumber = 1;
        document.querySelectorAll(".with-monaco").forEach((withMonacoElement) => {
            let language = "html",
                target = null;
            if (withMonacoElement.classList.contains("with-monaco-html")) {
                language = "html";
                target = document.getElementById("editor-container-footer");
            }
            if (withMonacoElement.classList.contains("with-monaco-css")) {
                language = "css";
                target = document.getElementById("editor-container-css");
            }

            const editorId = `editor-${idNumber}`;
            appendItemDivToItemsContainer(editorId);
            idNumber++;

            const textarea = withMonacoElement.querySelector("textarea");

            initMonacoEditor({ targetId: editorId, textareaId: textarea.id, language })

            const label =
                withMonacoElement.querySelector(".acf-label label").innerText;

            createFieldButtons({
                text: label,
                editorId,
                initEditor: () => initMonacoEditor({
                    targetId: editorId,
                    textareaId: textarea.id,
                    language
                }),
                textareaId: textarea.id,
                language
            });
        });
    });
};

initMonacoEditors();