module.exports = {
  apps: [
    {
      name: 'aiagen-wa-manager',
      script: 'manager.js',
      cwd: './wa-gateway',
      watch: false,
      instances: 1,
      autorestart: true,
      max_memory_restart: '1G',
      env: {
        NODE_ENV: 'production',
      }
    },
    {
      name: 'aiagen-python-ai',
      script: 'main.py',
      cwd: './ai-agent',
      interpreter: 'python', // Pastikan di server perintahnya 'python' atau 'python3'
      watch: false,
      instances: 1,
      autorestart: true,
      max_memory_restart: '500M',
      env: {
        PYTHONUNBUFFERED: '1',
      }
    }
  ]
};
