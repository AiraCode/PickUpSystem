const { makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const qrcode = require('qrcode-terminal');
const express = require('express');

const app = express();
app.use(express.json());

let sock;

async function connectToWhatsApp() {
  const { state, saveCreds } = await useMultiFileAuthState('baileys_auth_info');

  sock = makeWASocket({
    auth: state,
    printQRInTerminal: false,
  });

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      console.log('Scan this QR code:');
      qrcode.generate(qr, { small: true });
    }

    if (connection === 'close') {
      const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
      console.log('Connection closed. Reconnecting...', shouldReconnect);
      if (shouldReconnect) {
        connectToWhatsApp();
      }
    } else if (connection === 'open') {
      console.log('✅ WhatsApp connected successfully via Baileys!');
    }
  });
}

connectToWhatsApp();

app.post('/send', async (req, res) => {
  const { target, message } = req.body;

  if (!sock) {
    return res.status(503).json({ success: false, error: 'WhatsApp client is not ready.' });
  }

  let formattedPhone = target.replace(/[^0-9]/g, '');
  if (formattedPhone.startsWith('0')) {
    formattedPhone = '62' + formattedPhone.slice(1);
  }
  const id = `${formattedPhone}@s.whatsapp.net`;

  try {
    await sock.sendMessage(id, { text: message });
    return res.json({ success: true, message: 'Message sent!' });
  } catch (error) {
    return res.status(500).json({ success: false, error: error.message });
  }
});

app.listen(3000, () => {
  console.log('🚀 WA Bridge API listening on http://localhost:3000');
});

const fs = require('fs');
const path = require('path');

// Endpoint to force logout & clear session
app.post('/logout', async (req, res) => {
  try {
    if (sock) {
      await sock.logout(); // Logs out from WA servers
      sock.end(undefined); // Closes socket connection
    }

    // Delete local auth folder
    const authPath = path.join(__dirname, 'baileys_auth_info');
    if (fs.existsSync(authPath)) {
      fs.rmSync(authPath, { recursive: true, force: true });
    }

    return res.json({ success: true, message: 'Logged out successfully. Restart server to scan a new QR code.' });
  } catch (error) {
    return res.status(500).json({ success: false, error: error.message });
  }
});