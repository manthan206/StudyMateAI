const sendBtn = document.getElementById("sendBtn");
const message = document.getElementById("message");
const chatBox = document.getElementById("chatBox");

sendBtn.addEventListener("click", sendMessage);

message.addEventListener("keydown", function (e) {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

async function sendMessage() {

    let text = message.value.trim();

    if (text === "") return;

    // User Message
    chatBox.innerHTML += `
        <div class="user-message">
            ${escapeHtml(text)}
        </div>
    `;

    message.value = "";

    chatBox.scrollTop = chatBox.scrollHeight;

    // Typing Indicator
    chatBox.innerHTML += `
        <div class="bot-message" id="typing">
            🤖 Thinking...
        </div>
    `;

    chatBox.scrollTop = chatBox.scrollHeight;

    try {

        const response = await fetch("chat.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                message: text
            })

        });

        const data = await response.json();

        document.getElementById("typing").remove();

        if (data.success) {

            chatBox.innerHTML += `
                <div class="bot-message">
                    ${formatText(data.answer)}
                </div>
            `;

        } else {

            chatBox.innerHTML += `
                <div class="bot-message">
                    ❌ ${escapeHtml(data.message)}
                </div>
            `;

        }

    } catch (error) {

        document.getElementById("typing").remove();

        chatBox.innerHTML += `
            <div class="bot-message">
                ❌ Unable to connect to server.
            </div>
        `;

    }

    chatBox.scrollTop = chatBox.scrollHeight;

}


function escapeHtml(text){

    const div=document.createElement("div");

    div.innerText=text;

    return div.innerHTML;

}

function formatText(text){

    let html = marked.parse(text);

    setTimeout(() => {
        document.querySelectorAll("pre code").forEach((block)=>{
            hljs.highlightElement(block);
        });

        addCopyButtons();
    },100);

    return html;

}
window.onload = function () {
    loadHistory();
};


async function loadHistory() {

    try {

        const response = await fetch("get_history.php");

        const chats = await response.json();

        const historyList = document.getElementById("historyList");

        historyList.innerHTML = "";

        if (chats.length === 0) {

            historyList.innerHTML = `
                <li>No chats yet</li>
            `;

            return;
        }

        chats.forEach(chat => {

            let title = chat.question;

            if (title.length > 30)
                title = title.substring(0, 30) + "...";

            historyList.innerHTML += `
                <li onclick="loadChat(${chat.id})">
                    ${escapeHtml(title)}
                </li>
            `;

        });

    } catch (e) {

        console.log(e);

    }

}
async function loadChat(id){

    const response = await fetch("get_history.php");

    const chats = await response.json();

    const chat = chats.find(c => c.id == id);

    if(!chat) return;

    chatBox.innerHTML = `
        <div class="user-message">
            ${escapeHtml(chat.question)}
        </div>

        <div class="bot-message">
            ${formatText(chat.answer)}
        </div>
    `;

    chatBox.scrollTop = chatBox.scrollHeight;

}
document.getElementById("newChat").addEventListener("click", function(){

    chatBox.innerHTML = `
        <div class="bot-message">

            👋 New conversation started.

        </div>
    `;

});
function addCopyButtons(){

    document.querySelectorAll("pre").forEach((pre)=>{

        if(pre.querySelector(".copy-btn")) return;

        const btn=document.createElement("button");

        btn.innerHTML="Copy";

        btn.className="copy-btn";

        btn.onclick=()=>{

            navigator.clipboard.writeText(
                pre.querySelector("code").innerText
            );

            btn.innerHTML="Copied!";

            setTimeout(()=>{

                btn.innerHTML="Copy";

            },1500);

        };

        pre.prepend(btn);

    });

}
document.getElementById("exportPdf").onclick=function(){

    const {jsPDF}=window.jspdf;

    const pdf=new jsPDF();

    pdf.text(chatBox.innerText,10,10);

    pdf.save("StudyMateAI-Chat.pdf");

}
document.getElementById("themeBtn").onclick=()=>{

document.body.classList.toggle("dark");

localStorage.setItem(
"theme",
document.body.classList.contains("dark")
);

};

if(localStorage.getItem("theme")=="true"){

document.body.classList.add("dark");

}
async function sendFeedback(chatId, rating) {

    console.log("Chat ID:", chatId);
    console.log("Rating:", rating);

    try {

        const response = await fetch("feedback.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({
                chat_id: chatId,
                rating: rating
            })

        });

        const data = await response.json();

        console.log(data);

        if (data.success) {
            alert("Thank you for your feedback!");
        } else {
            alert(data.message);
        }

    } catch (e) {

        console.error(e);

    }

}
// A quick regex fix for the frontend if the backend is locked
const fixedRawData = rawText
  .replace(/"answer"\./, '"answer":')
  .replace(/"chat_id"\./, '"chat_id":');

const data = JSON.parse(fixedRawData);
console.log(data.answer); 
console.log(data.chat_id); // "14"