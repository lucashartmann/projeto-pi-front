import { getCaminhoRelativo } from "./modules/utils.js";

Inputmask("999.999.999-99").mask("#cpf_cnpj");

window.togglePasswordVisibility = togglePasswordVisibility;
window.verificaDataNascimento = verificaDataNascimento;
window.verificaSenha = verificaSenha;
window.fazerLogin = fazerLogin;
window.enviarNovaSenha = enviarNovaSenha;
window.voltarLogin = voltarLogin;
window.novaSenha = novaSenha;

async function enviarNovaSenha() {
    event.preventDefault();
    const email = document.getElementById("email").value;
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=recuperar_senha");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                email: email
            })
        });
        if (!resposta.ok) {
            alert("Erro ao recuperar senha: ");
            return null;
        }
        let dados = null;
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        if (dados.status == "erro") {
            alert(dados.mensagem);
            return;
        }

        if (resposta.ok && dados.status == "sucesso") {
            alert(dados.mensagem);
            return;
            // window.location.href = "../index.html";
        }

    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}




function togglePasswordVisibility(event) {
    const senhaInput = event.target.previousElementSibling;
    const toggleIcons = event.target;

    if (senhaInput.type === "password") {
        senhaInput.type = "text";
        toggleIcons.classList.remove("fa-eye-slash");
        toggleIcons.classList.add("fa-eye");
    } else {
        senhaInput.type = "password";
        toggleIcons.classList.remove("fa-eye");
        toggleIcons.classList.add("fa-eye-slash");
    }

}

function verificaDataNascimento() {
    const dataInput = document.getElementById("data-nascimento");
    const dataNascimento = new Date(dataInput.value);
    const hoje = new Date();
    const idadeMinima = 18;
    const idade = hoje.getFullYear() - dataNascimento.getFullYear();

    if (idade < idadeMinima || (idade === idadeMinima && hoje.getMonth() < dataNascimento.getMonth()) || (idade === idadeMinima && hoje.getMonth() === dataNascimento.getMonth() && hoje.getDate() < dataNascimento.getDate())) {
        dataInput.setCustomValidity("Você deve ter pelo menos 18 anos para se cadastrar.");
        dataInput.reportValidity();
        dataInput.focus();
    } else {
        dataInput.setCustomValidity("");
    }
}

function verificaSenha() {
    const senhaInput = document.getElementById("senha-cadastro");
    const senha = senhaInput.value;
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!#%*?&])[A-Za-z\d@$!#%*?&]{8,}$/;
    if (!regex.test(senha)) {
        senhaInput.setCustomValidity("A senha deve conter pelo menos 8 caracteres, incluindo letras maiúsculas, minúsculas, números e caracteres especiais.");
        senhaInput.reportValidity();
        senhaInput.focus();
    } else {
        senhaInput.setCustomValidity("");

    }
}

async function fazerLogin(event) {
    event.preventDefault();
    const usuario = document.getElementById("usuario").value;
    const senha = document.getElementById("senha").value;

    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=login");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                usuario: usuario,
                senha: senha
            })
        });
        if (resposta.erro) {
            alert("Erro ao fazer login: " + resposta.erro);
            return null;
        }
        const contentType = resposta.headers.get("content-type");
        let dados = null;
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        console.log("Dados recebidos do login:", dados);

        if (dados.status == "erro") {
            alert(dados.mensagem);
            return;
        }

        if (resposta.ok && dados.status == "sucesso") {
            if (dados.usuario.tipo == "CLIENTE") {
                window.location.href = "../index.html";
                return;
            }
            window.location.href = "../html/cadastro-imovel.html";
            return;
        } else {
            alert("Login invalido!");
        }


    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}

document.getElementById("h3-login").addEventListener("click", function () {
    document.getElementById("form-login").style.display = "flex";
    document.getElementById("form-cadastro").style.display = "none";
    document.getElementById("form-nova-senha").style.display = "none";
    document.getElementById("h3-login").style.color = "var(--hover)";
    document.getElementById("h3-login").style.opacity = "1";
    document.getElementById("h3-cadastro").style.color = "white";
    document.getElementById("h3-cadastro").style.opacity = "0.6";
    document.getElementById("login-header").style.top = "26%";
});

document.getElementById("h3-cadastro").addEventListener("click", function () {
    document.getElementById("form-login").style.display = "none";
    document.getElementById("form-cadastro").style.display = "flex";
    document.getElementById("form-nova-senha").style.display = "none";
    document.getElementById("h3-cadastro").style.color = "var(--hover)";
    document.getElementById("h3-cadastro").style.opacity = "1";
    document.getElementById("h3-login").style.color = "white";
    document.getElementById("h3-login").style.opacity = "0.6";
    document.getElementById("login-header").style.top = "28%";
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("form-cadastro").style.display = "none";
    document.getElementById("h3-login").style.color = "var(--hover)";
    document.getElementById("h3-login").style.opacity = "1";
    document.getElementById("h3-cadastro").style.color = "white";
});

async function fazerCadastro() {
    event.preventDefault();
    var formAnuncio = document.getElementById("form-cadastro");
    if (!formAnuncio) return;
    var data = {};
    var formData = new FormData(formAnuncio);
    formData.forEach(function (value, key) {
        data[key] = value;
    });

    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=cadastro");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });
        if (resposta.erro) {
            alert("Erro ao fazer cadastro: " + resposta.erro);
            return null;
        }
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            const data = resposta.status !== 204 ? await resposta.json() : {};
            if (data.length < 1) {
                alert("Resposta inesperada do servidor: JSON vazio");
                console.error("Resposta JSON vazia:", data);
                return;
            } else {
                if (data.status == "erro") {
                    alert(data.mensagem);
                    return;
                }

                if (resposta.ok && data.status == "sucesso") {
                    document.getElementById("form-login").style.display = "flex";
                    document.getElementById("form-cadastro").style.display = "none";
                    document.getElementById("h3-login").style.color = "var(--background-nav)";
                    document.getElementById("h3-cadastro").style.color = "white";
                    document.getElementById("login-header").style.top = "26%";
                    window.location.href = "../index.html";
                    return;
                }
            }
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        alert("Login invalido!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}

function novaSenha() {
    event.preventDefault();
    const formLogin = document.getElementById("form-login");
    formLogin.style.display = "none";
    const formNovaSenha = document.getElementById("form-nova-senha");
    formNovaSenha.style.display = "flex";
}

function voltarLogin() {
    event.preventDefault();
    const formLogin = document.getElementById("form-login");
    formLogin.style.display = "flex";
    const formNovaSenha = document.getElementById("form-nova-senha");
    formNovaSenha.style.display = "none";
}

window.handleCredentialResponse = handleCredentialResponse;

async function handleCredentialResponse(response) {
    event.preventDefault();
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=login");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                credential: response.credential
            })
        });
        if (resposta.erro) {
            alert("Erro ao fazer login: " + resposta.erro);
            return null;
        }
        const contentType = resposta.headers.get("content-type");
        let dados = null;
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        if (dados.status == "erro") {
            alert(dados.mensagem);
            return;
        }

        if (resposta.ok && dados.status == "sucesso") {
            if (dados.usuario.tipo == "CLIENTE") {
                window.location.href = "../index.html";
                return;
            }
            window.location.href = "../html/cadastro-imovel.html";
            return;
        }

        alert("Login invalido!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}

window.addEventListener("DOMContentLoaded", function () {
    const formLogin = document.getElementById("form-login");
    formLogin.addEventListener("submit", fazerLogin);
    const formCadastro = document.getElementById("form-cadastro");
    formCadastro.addEventListener("submit", fazerCadastro);
    const formNovaSenha = document.getElementById("form-nova-senha");
    formNovaSenha.addEventListener("submit", enviarNovaSenha);
});