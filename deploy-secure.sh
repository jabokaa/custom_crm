#!/bin/bash

# Deploy script que usa variáveis de ambiente para credenciais seguras
# Para usar com GitHub Actions, configure os seguintes secrets no repositório:
# - SSH_PASSWORD: senha SSH do servidor
# - SSH_USER: usuário SSH (ex: u139964339)
# - SSH_HOST: host SSH (ex: 89.117.7.145)
# - SSH_PORT: porta SSH (ex: 65002)
# - DEPLOY_PATH: caminho no servidor (ex: domains/whitesmoke-owl-165796.hostingersite.com/)
# - DB_PASSWORD: senha do banco de dados

set -e  # Para na primeira falha

# Verificar se as variáveis de ambiente necessárias estão definidas
if [ -z "$SSH_PASSWORD" ] || [ -z "$SSH_USER" ] || [ -z "$SSH_HOST" ]; then
    echo "❌ Erro: Variáveis de ambiente SSH não configuradas"
    echo "Configure SSH_PASSWORD, SSH_USER e SSH_HOST"
    exit 1
fi

# Definir valores padrão para variáveis opcionais
SSH_PORT=${SSH_PORT:-22}
DEPLOY_PATH=${DEPLOY_PATH:-""}

# Limpar cache npm e node_modules se existir
echo "🧹 Limpando cache e dependências antigas..."
rm -rf node_modules package-lock.json
npm cache clean --force

# Instalar dependências e compilar assets localmente
echo "📦 Instalando dependências do Node.js localmente..."
npm install --legacy-peer-deps
echo "🔨 Compilando assets localmente..."
npm run build

# Criar arquivo temporário com o repositório atual
TEMP_FILE=$(mktemp /tmp/repo_backup_XXXXXX.tar.gz)

# Comprimir repositório atual (incluindo os assets compilados)
echo "📦 Comprimindo repositório..."
tar -czf "$TEMP_FILE" --exclude='.git' --exclude='vendor' --exclude='node_modules' .

echo "✅ Repositório salvo em: $TEMP_FILE"

# Enviar arquivo para o servidor remoto
echo "🚀 Enviando arquivo para o servidor..."
REMOTE_FILE="repo_backup_$(date +%Y%m%d_%H%M%S).tar.gz"

if [ -n "$DEPLOY_PATH" ]; then
    FULL_REMOTE_PATH="${DEPLOY_PATH}${REMOTE_FILE}"
else
    FULL_REMOTE_PATH="$REMOTE_FILE"
fi

sshpass -p "$SSH_PASSWORD" scp -o StrictHostKeyChecking=no -P "$SSH_PORT" "$TEMP_FILE" "${SSH_USER}@${SSH_HOST}:${FULL_REMOTE_PATH}"

# Conectar via SSH, extrair arquivo e configurar
echo "🔧 Conectando ao servidor e configurando aplicação..."
sshpass -p "$SSH_PASSWORD" ssh -o StrictHostKeyChecking=no -p "$SSH_PORT" "${SSH_USER}@${SSH_HOST}" << EOF
set -e
cd ${DEPLOY_PATH}
echo "📂 Extraindo arquivo $REMOTE_FILE..."
tar -xzf "$REMOTE_FILE"
echo "🗑️ Removendo arquivo comprimido..."
rm "$REMOTE_FILE"
echo "🗂️ Removendo pasta public_html existente..."
rm -rf public_html
echo "📁 Renomeando pasta public para public_html..."
mv public public_html
echo "⚙️ Copiando arquivo .env.example para .env..."
cp .env.example .env

# Configurar variáveis de ambiente
echo "🔐 Configurando variáveis de ambiente..."
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's|APP_URL=http://localhost|APP_URL=https://darksalmon-kingfisher-626375.hostingersite.com|' .env
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
sed -i 's/# DB_PORT=3306/DB_PORT=3306/' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=u139964339_bradev/' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=u139964339_bradev/' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=localhost/' .env

# Usar senha do banco de dados da variável de ambiente se disponível
sed -i "s/# DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env


echo "🎨 Configurando Vite para produção..."
sed -i 's|VITE_APP_NAME="\${APP_NAME}"|VITE_APP_NAME="TransWells"|' .env
echo "ASSET_URL=https://darksalmon-kingfisher-626375.hostingersite.com" >> .env

echo "📚 Instalando dependências do Composer..."
composer install --optimize-autoloader --no-dev --no-interaction

echo "🔑 Gerando chave da aplicação..."
php artisan key:generate --force --no-interaction

echo "🔍 Verificando configuração..."
grep "APP_KEY=" .env

echo "🗃️ Executando migrations..."
php artisan migrate --force

echo "📦 Copiando assets do build..."
if [ -d "public/build" ]; then
    cp -r public/build public_html/build
    echo "✅ Assets copiados para public_html/build"
else
    echo "⚠️ Pasta public/build não encontrada!"
fi

echo "🔗 Criando link simbólico..."
if [ ! -e "public" ]; then
    ln -sf public_html public
    echo "✅ Link simbólico criado"
fi

echo "🛡️ Ajustando permissões..."
chmod -R 755 public_html/
chmod -R 775 storage/ bootstrap/cache/ 2>/dev/null || true

echo "🧹 Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🌱 Executando seeders..."
php artisan db:seed --force

echo "🎉 Deploy concluído com sucesso!"
exit
EOF

# Remover arquivo temporário local
rm "$TEMP_FILE"
echo "🗑️ Arquivo temporário local removido"
echo "✅ Deploy finalizado!"

