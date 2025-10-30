function login(){

var email=document.getElementById('email').value;
var password=document.getElementById('password').value;

if(email=="admin" && password=="admin"){
    alert('sucesso');
    location.href="home.html";
}else{
    alert('usuario ou senha incorreto');
    }

}
