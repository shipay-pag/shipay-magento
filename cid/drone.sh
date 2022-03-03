#! /bin/sh

# # execute este script apenas como parte do pipeline.
# # [ -z  " $CI " ] &&  echo  " variável de ambiente ci ausente "  &&  exit 2

# só executa o script quando o token do magento existe.
[ -z  " $SSH_KEY " ] &&  echo  " faltando a chave ssh "  &&  exit 3

# escreve a chave ssh.
mkdir /root/.ssh
echo -n " ​​$SSH_KEY "  > /root/.ssh/id_rsa
chmod 600 /root/.ssh/id_rsa

# adicione magento.com aos nossos hosts conhecidos.
toque em /root/.ssh/known_hosts
chmod 600 /root/.ssh/known_hosts
ssh-keyscan -H magento.shipay.com.br > /etc/ssh/ssh_known_hosts 2> /dev/null
