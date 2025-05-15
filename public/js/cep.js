function buscarCEP(cep) {
    cep = cep.replace(/\D/g, '');

    if (cep.length !== 8 || isNaN(cep)) {
        alert("Digite um CEP válido com 8 números.");
        return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
            if (!data.erro) {
                document.querySelector("#endereco").value = data.logradouro;
                document.querySelector("#bairro").value = data.bairro;
                document.querySelector("#cidade").value = data.localidade;
                document.querySelector("#estado").value = data.uf;
            } else {
                alert("CEP não encontrado.");
            }
        })
        .catch(() => {
            alert("Erro ao buscar o CEP.");
        });
}
