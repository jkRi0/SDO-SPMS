// Import Firebase modules from CDN
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import {
  getDatabase,
  ref,
  onValue,
  set
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-database.js";

// ================================
// Firebase setup (step by step)
// ================================
// 1) Go to https://console.firebase.google.com
//    - Click "Add project" / "Create project" and follow the wizard.
// 2) Add a Web App to the project
//    - In the project overview, click the web icon (</>). 
//    - Give the app a nickname and click "Register app".
// 3) Copy the firebaseConfig object
//    - After registering the app, Firebase shows example code.
//    - Copy the const firebaseConfig = { ... } block from there.
// 4) Enable Realtime Database
//    - Left menu: Build → Realtime Database → Create database.
//    - Choose a location and start in test mode for this demo.
// 5) (Optional) Check Database rules
//    - For local demos, rules can be open; for production, lock them down.
// 6) Paste your real config below
//    - Replace all placeholder strings (YOUR_API_KEY, YOUR_PROJECT_ID, etc.).

// TODO: Replace this config with your actual Firebase project config
// Get it from Firebase Console → Project settings → General → Your apps → Web app
const firebaseConfig = {
  apiKey: "AIzaSyASnvxxqoEMOt0nkG1-LrsXKKp6q7IYm_4",
  authDomain: "sample-forcollabaccount.firebaseapp.com",
  databaseURL: "https://sample-forcollabaccount-default-rtdb.asia-southeast1.firebasedatabase.app/",
  projectId: "sample-forcollabaccount",
  storageBucket: "sample-forcollabaccount.firebasestorage.app",
  messagingSenderId: "403446463294",
  appId: "1:403446463294:web:29493f60adb8fa94f5ca6f"
};

// Initialize Firebase app and Realtime Database
const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

// DOM elements
const loginCard = document.getElementById("loginCard");
const appCard = document.getElementById("appCard");
const usernameInput = document.getElementById("usernameInput");
const passwordInput = document.getElementById("passwordInput");
const loginBtn = document.getElementById("loginBtn");
const loginError = document.getElementById("loginError");

const messageInput = document.getElementById("messageInput");
const saveBtn = document.getElementById("saveBtn");
const statusEl = document.getElementById("status");

// Hard-coded sample credentials for demo only
const SAMPLE_USERNAME = "admin";
const SAMPLE_PASSWORD = "admin";

// Handle login click: simple client-side check
loginBtn.addEventListener("click", () => {
  const u = usernameInput.value.trim();
  const p = passwordInput.value.trim();

  if (u === SAMPLE_USERNAME && p === SAMPLE_PASSWORD) {
    // Successful pseudo-login
    loginError.textContent = "";
    loginCard.classList.add("hidden");
    appCard.classList.remove("hidden");
    statusEl.textContent = "Logged in as admin. Listening for realtime updates...";
    startRealtimeListener();
  } else {
    // Show error message for wrong credentials
    loginError.textContent = "Invalid username or password.";
  }
});

// Start listening for realtime updates on the shared message
function startRealtimeListener() {
  // Path in Realtime Database where the shared message is stored
  const messageRef = ref(db, "shared/message");

  // onValue will fire whenever data at this path changes
  onValue(messageRef, (snapshot) => {
    const value = snapshot.val();
    if (value !== null && value !== undefined) {
      // Update the input field with the latest value
      messageInput.value = value;
      statusEl.textContent = "Message synced at " + new Date().toLocaleTimeString();
    } else {
      statusEl.textContent = "No message set yet.";
    }
  });
}

// Save button writes the new message to the shared path
saveBtn.addEventListener("click", async () => {
  const newMessage = messageInput.value;
  const messageRef = ref(db, "shared/message");

  statusEl.textContent = "Saving...";
  try {
    // set() overwrites the value at this path
    await set(messageRef, newMessage);
    statusEl.textContent = "Saved! Waiting for realtime update...";
  } catch (err) {
    console.error(err);
    statusEl.textContent = "Error saving message. Check console.";
  }
});
