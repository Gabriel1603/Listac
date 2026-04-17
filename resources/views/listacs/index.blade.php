<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listac</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #bdbaba;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topo h1 {
            margin: 0;
            color: #333;
        }

        .topo button {
            background: #4facfe;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;

        }

        .topo button:hover {
            background: #3b8de0;
        }

        .lista {
            margin-top: 20px;
        }

        .tarefa {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
        }

        .tarefa:hover {
            transform: scale(1.01);
        }

        .concluida {
            opacity: 0.6;
        }

        .status {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            color: white;
            display: inline-block;
            margin-top: 5px;
        }

        .pendente { background: #ffc107; }
        .finalizada { background: #28a745; }

        .btn {
            border: none;
            padding: 8px 10px;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            margin-left: 5px;
        }

        .concluir { background: #28a745; }
        .editar { background: #17a2b8; }
        .excluir { background: #dc3545; }

        .btn:hover {
            opacity: 0.85;
        }

        .modal {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(0,0,0,0.4);
        }

        .modal-content {
            background: white;
            width: 350px;
            margin: 100px auto;
            padding: 20px;
            border-radius: 10px;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        input, textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn-modal {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        .btn-salvar {
            background: #4facfe;
            color: white;
        }

        .btn-cancelar {
            background: #ccc;
        }

    </style>
</head>
<body>

<div class="container">

    <div class="topo">
        <h1>Listac</h1>
        <button onclick="abrirModal()">+ Adicionar</button>
    </div>

    <div class="lista" id="listaTarefas"></div>

</div>

<div class="modal" id="modal">
    <div class="modal-content">
        <h3 id="tituloModal">Nova Tarefa</h3>

        <input type="text" id="titulo" placeholder="Título" maxlength="50">
        <textarea id="descricao" placeholder="Descrição"></textarea>

        <label>Data de criação:</label>
        <input type="date" id="dataCriacao" readonly>

        <label>Data de conclusão:</label>
        <input type="date" id="dataConclusao">

        <button id="btnSalvar" onclick="salvarTarefa()" class="btn-modal btn-salvar">Salvar</button>

        <button id="btnConcluirModal" onclick="concluirPeloModal()" class="btn-modal concluir" style="display:none;">
            Concluir Tarefa
        </button>

        <button onclick="fecharModal()" class="btn-modal btn-cancelar">Fechar</button>
    </div>
</div>

<script>
let tarefas = [];
let editandoId = null;


function abrirModal() {
    editandoId = null;

    document.getElementById("tituloModal").innerText = "Nova Tarefa";
    document.getElementById("btnConcluirModal").style.display = "none";
    document.getElementById("btnSalvar").style.display = "block";

    desbloquearCampos();

    let hoje = new Date().toISOString().split("T")[0];
    document.getElementById("dataCriacao").value = hoje;

    document.getElementById("titulo").value = "";
    document.getElementById("descricao").value = "";
    document.getElementById("dataConclusao").value = "";

    document.getElementById("modal").style.display = "block";
}

function fecharModal() {
    document.getElementById("modal").style.display = "none";
}

function bloquearCampos() {
    titulo.disabled = true;
    descricao.disabled = true;
    dataConclusao.disabled = true;
}

function desbloquearCampos() {
    titulo.disabled = false;
    descricao.disabled = false;
    dataConclusao.disabled = false;
}

function salvarTarefa() {
    let tituloVal = titulo.value.trim();
    let descricaoVal = descricao.value.trim();
    let dataCriacao = document.getElementById("dataCriacao").value;
    let dataConclusao = document.getElementById("dataConclusao").value;

    if (!tituloVal || tituloVal.length < 3) return alert("Título inválido!");
    if (!dataConclusao) return alert("Informe a data!");
    if (dataConclusao < dataCriacao) return alert("Data inválida!");

    if (editandoId) {
        tarefas = tarefas.map(t => t.id === editandoId
            ? { ...t, titulo: tituloVal, descricao: descricaoVal, dataConclusao }
            : t
        );
    } else {
        tarefas.push({
            id: Date.now(),
            titulo: tituloVal,
            descricao: descricaoVal,
            dataCriacao,
            dataConclusao,
            status: "Pendente"
        });
    }

    renderizar();
    fecharModal();
}

function concluir(id) {
    let t = tarefas.find(t => t.id === id);
    if (t.status === "Concluída") return;

    if (!confirm("Concluir tarefa?")) return;

    t.status = "Concluída";
    renderizar();
}

function concluirPeloModal() {
    let t = tarefas.find(t => t.id === editandoId);
    if (t.status === "Concluída") return;

    if (!confirm("Concluir tarefa?")) return;

    t.status = "Concluída";
    renderizar();
    fecharModal();
}

function editar(id) {
    let t = tarefas.find(t => t.id === id);
    editandoId = id;

    titulo.value = t.titulo;
    descricao.value = t.descricao;
    dataCriacao.value = t.dataCriacao;
    dataConclusao.value = t.dataConclusao;

    modal.style.display = "block";

    if (t.status === "Concluída") {
        tituloModal.innerText = "Visualizar Tarefa";
        bloquearCampos();
        btnSalvar.style.display = "none";
        btnConcluirModal.style.display = "none";
    } else {
        tituloModal.innerText = "Editar Tarefa";
        desbloquearCampos();
        btnSalvar.style.display = "block";
        btnConcluirModal.style.display = "block";
    }
}

function remover(id) {
    if (!confirm("Excluir tarefa?")) return;
    tarefas = tarefas.filter(t => t.id !== id);
    renderizar();
}

function renderizar() {
    listaTarefas.innerHTML = "";

    let ordenadas = tarefas.sort((a,b) => a.status === "Pendente" ? -1 : 1);

    ordenadas.forEach(t => {
        let statusClasse = t.status === "Concluída" ? "finalizada" : "pendente";

        listaTarefas.innerHTML += `
            <div class="tarefa ${t.status === "Concluída" ? "concluida" : ""}">
                <div>
                    <strong>${t.titulo}</strong><br>
                    ${t.descricao || ""}<br>
                    <small>Criação: ${t.dataCriacao}</small><br>
                    <small>Conclusão: ${t.dataConclusao}</small><br>
                    <span class="status ${statusClasse}">${t.status}</span>
                </div>

                <div>
                    ${t.status === "Pendente" ? `<button onclick="concluir(${t.id})" class="btn concluir"><i class="fas fa-check"></i></button>` : ``}
                    <button onclick="editar(${t.id})" class="btn editar"><i class="fas fa-eye"></i></button>
                    <button onclick="remover(${t.id})" class="btn excluir"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
    });
}

renderizar();
</script>

</body>
</html>