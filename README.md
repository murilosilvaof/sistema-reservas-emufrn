# sistema-reservas-emufrn
Sistema de Reservas de Espaços - EMUFRN

Este projeto é um sistema desenvolvido para a **Escola de Música da UFRN (EMUFRN)** com o objetivo de facilitar o agendamento de espaços como auditórios para eventos, ensaios e recitais.

## 📋 Funcionalidades

- Seleção de espaço: Auditório Onofre Lopes (Grande) ou Auditório Oriano de Almeida (Pequeno).
- Exibição de calendário de disponibilidade de horários.
- Formulário inteligente de reserva.
- Envio automático de e-mail para o responsável, com opção de **confirmar** ou **recusar** a reserva.
- Atualização de status no banco de dados (`confirmada` ou `recusada`).
- Layout moderno, responsivo e adaptado para dispositivos móveis.
- Animações suaves de carregamento e transição de tela.

## 🏛️ Espaços disponíveis

| Espaço | Capacidade |
|:---|:---|
| Auditório Onofre Lopes (Grande) | 225 lugares |
| Auditório Oriano de Almeida (Pequeno) | 70 lugares |

## 🛠️ Tecnologias Utilizadas

- HTML5
- CSS3
- JavaScript (puro)
- PHP
- MySQL
- PHPMailer (para envio de e-mails)
- Git e GitHub

## 🚀 Como rodar o projeto localmente

1. Clone o repositório:

```bash
git clone https://github.com/seu-usuario/seu-repo.git
Instale o PHPMailer (caso não tenha):

bash
Copiar
Editar
composer require phpmailer/phpmailer
Configure o banco de dados reservas_emufrn no MySQL.

Ajuste o arquivo processa_reserva.php com as suas credenciais de banco de dados e e-mail.

Rode o projeto em servidor local (XAMPP, WAMP ou similar).

📩 Contato
Desenvolvido por Murilo Francisco da Silva
