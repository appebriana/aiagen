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
    }
  ]
};
