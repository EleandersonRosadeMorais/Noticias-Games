# 🎮 Notícias Games - Portal de Notícias de Games

> Site web em **PHP** para visualização e publicação de notícias sobre games com sistema de autenticação e comentários!

---

## 📱 Visão Geral

O **Notícias Games** é um portal completo desenvolvido em PHP para entusiastas de games. O sistema oferece:

- 👥 **Três tipos de usuários**: Visitantes, Autores e Administrador
- 📝 **Sistema de publicação** para autores cadastrados
- 💬 **Comentários** em notícias
- 🎨 **Interface temática** com design gamer
- 🔐 **Sistema seguro** de autenticação

---

## Funcionalidades

### 👤 Para Visitantes
- ✅ Visualizar notícias
- ✅ Ler notícias completas
- ✅ Comentar nas publicações

### ✍️ Para Autores
- ✅ Sistema de login seguro
- ✅ Publicar novas notícias
- ✅ Gerenciar próprias publicações (editar/excluir)
- ✅ Upload de imagens

### ⚙️ Para Administrador
- ✅ Portal administrativo
- ✅ Gerenciar autores
- ✅ Supervisão completa do sistema

### 🔧 Técnicas
- ✅ Validação de formulários
- ✅ Persistência em banco SQL
- ✅ Interface responsiva
- ✅ Animações CSS

---

## 🛠️ Tecnologias Utilizadas

- **PHP** 7.x+ - Backend e lógica de negócio
- **MySQL** - Banco de dados relacional
- **HTML5** - Estrutura semântica
- **CSS3** - Estilização e animações
- **XAMPP** - Ambiente de desenvolvimento

---

## ⚙️ Como Executar

**Pré-requisitos:**
- Visual Studio Code
- Xampp (apache e SQL)
- Banco de dados local

**Passos:**
```bash
# 1. Abra o git bash e entre na pasta htdocs do Xampp
cd "/c/xampp/htdocs"

# 2. Clone o repositório 
git clone https://github.com/EleandersonRosadeMorais/NoticiasGames

# 3. Abra o seu navegador e pesquise na URL
localhost/NoticiasGames/criar_banco

# 4. Agora novamente na URL pesquisa a pagina inicial do projeto
localhost/NoticiasGames/Noticia/paginaPrincipal

---

## 📂 Estrutura do Projeto

```bash
📦 NoticiasGames
├── 📂 admin/
│    └── portalAdmin.php
├── 📂 Autor/
│    ├── deletar.php
│    ├── editarHtml.php
│    ├── login.php
│    ├── logout.php
│    └── registrarHtml.php
├── 📂 classes/
│    ├── Autor.php
│    ├── Comentario.php
│    ├── Database.php
│    └── Noticia.php
├── 📂 components/
│    ├── footer.php
│    ├── header_admin.php
│    ├── header_autor.php
│    ├── header_publico.php
│    └── header.php
├── 📂 config/
│    └── config.php
├── 📂 css/
│    └── style.css
├── 📂 Noticia/
│    ├── adicionarNoticia.php
│    ├── deletarNoticia.php
│    ├── editarNoticiaHtml.php
│    ├── minhasNoticias.php
│    ├── noticiaCompleta.php
│    └── paginaPrincipal.php
├── 📂 Uploads/
├── Banco.bd
├── criar_banco.php
└── README.md
```

---


## 💻 Exemplo de Código

```php
/**
 * Registra uma nova notícia no sistema
 */
public function registrar($titulo, $noticia, $autor_fk, $imagem)
{
    $query = "INSERT INTO " . $this->table_name . " 
              (titulo, noticia, autor_fk, imagem) 
              VALUES (?, ?, ?, ?)";
    
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$titulo, $noticia, $autor_fk, $imagem]);
}

/**
 * Busca notícias com informações do autor
 */
public function lerComAutor()
{
    $query = "SELECT n.*, a.nome as autor_nome 
              FROM " . $this->table_name . " n 
              INNER JOIN autores a ON n.autor_fk = a.id 
              ORDER BY n.dataCriacao DESC";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
}
```

## 💬 Contato e Suporte

Tem dúvidas, sugestões ou encontrou algum bug? 

📧 **Email**: eleandersonmorais@gmail.com  
💼 **LinkedIn**: [Eleanderson Morais](https://www.linkedin.com/in/eleanderson-rosa-de-morais-9aaab9324/)  
🐙 **GitHub**: [EleandersonRosadeMorais](https://github.com/EleandersonRosadeMorais/)

### 🤝 Contribuições
Contribuições são bem-vindas! Sinta-se à vontade para:
- Reportar issues
- Sugerir novas funcionalidades
- Enviar pull requests