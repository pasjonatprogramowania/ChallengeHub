document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('dark-mode')) {
        document.documentElement.classList.toggle('dark-mode')
    }
    
});

function turnOnDarkMode() {
    console.log(localStorage.getItem('dark-mode'))
    if (localStorage.getItem('dark-mode')) {
        localStorage.setItem('dark-mode','');
    } else {
        localStorage.setItem('dark-mode', true);
    }
    document.documentElement.classList.toggle('dark-mode')
}
document.addEventListener('load',() => {
registerSW();
})

async function registerSW(){
    if('serviceWorker' in navigator){
        try{
            await navigator.serviceWorker.register('./sw.js');
        }catch (e) {
            console.log(`SW registration failed`);
        }
    }
  }