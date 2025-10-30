// ======= ANIMAÇÃO INICIAL DO FORMULÁRIO (GSAP) =======
gsap.set(".login-section form > *", { opacity: 0, y: 30 }); // Esconde e move os elementos para baixo

const tl = gsap.timeline();

tl.from("#welcome-title", { // Anima o título principal
    duration: .8, 
    y: -50,
    opacity: 0,
    ease: "power3.out"
})
.from("#welcome-subtitle", { // Anima o subtítulo
    duration: .6,
    y: -30,
    opacity: 0,
    ease: "power2.out"
}, "<0.2")
.to(".login-section form > *", { // Anima os campos do formulário
    duration: .5,
    opacity: 1,
    y: 0,
    stagger: .1,
    ease: "power1.out"
});

// ======= CARROSSEL DE IMAGENS DE FUNDO =======
const imageSection = document.getElementById("image-section");
const tempo = 6 * 1000; // Tempo entre trocas (6s)
let slideIndex = 0;

// CÓDIGO CORRETO PARA A ESTRUTURA COM PASTAS SEPARADAS

const imagens = [
    'url("../public/pexels-chris-f-38966-1283219.jpg")',
    'url("../public/pexels-cottonbro-3296280.jpg")',
    'url("../public/pexels-cottonbro-3297882.jpg")',
    'url("../public/pexels-pixabay-301692.jpg")',
    'url("../public/pexels-rajesh-tp-749235-2098085.jpg")'
];

const trocarBackground = () => { // Troca a imagem de fundo com transição
    if(slideIndex >= imagens.length)
        slideIndex = 0;

    imageSection.style.setProperty('--next-background', imagens[slideIndex]);
    imageSection.classList.add('fade-transition');

    setTimeout(() => {
        imageSection.style.backgroundImage = imagens[slideIndex];
        imageSection.classList.remove('fade-transition')
    }, 1000);

    slideIndex++;
}

trocarBackground(); // Inicializa o carrossel

setInterval(trocarBackground, tempo); // Troca a imagem periodicamente

