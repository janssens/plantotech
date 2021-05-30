document.addEventListener('DOMContentLoaded', function () {
    sendMessage();
}, false);

window.addEventListener('mypushstate',function (event){
    sendMessage();
});

function sendMessage(){
    if(top.location === self.location)
    {
        console.log('this page should be called in an iframe from '+parent_url);
        if (!embed_admin)
            window.location.href = parent_url;
    }else{
        let data = {};
        data.path = window.location.pathname;
        data.search = window.location.search;
        data.meta_title = document.querySelector('title').textContent;
        data.meta_description = document.querySelector('meta[name="description"]').textContent;
        window.parent.postMessage(data, '*');
    }
}