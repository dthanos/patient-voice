# 🏥 Medical Records & Audio Analysis Platform

## 📌 Overview

This is a full-stack web application that manages patient records and integrates an audio analysis feature that allows users to upload audio files, transcribe them into text, and generate AI-based summaries. The platform combines a Nuxt 3 frontend (with Vuetify and Pinia), a PHP-based backend API layer (using Laravel 12), and external services such as:

* **MockAPI** for simulated patient records,
* **AssemblyAI (Automatic Speech Recognition)** for audio transcription,
* **OpenAI (Large Language Model)** for summarization.

---

## 🔧 Prerequisites

Before setting up and running the project, ensure that the following tools are installed on your system:

- **[Docker](https://www.docker.com/)**

- **[Node.js (v20 or above)](https://nodejs.org/en/)**

---

## 🔧 Setup Instructions

### 📦 Infrastructure (Docker & Laravel Sail)

1. **Navigate to the backend folder**
   ```bash
   cd api
   ```
2. **Paste the .env file into the api folder**

3.  **Install and start Laravel Sail**
   ```bash
   composer require laravel/sail --dev --ignore-platform-reqs
   ./vendor/bin/sail up
```

### 📦 Backend (Laravel)

1. **On another terminal navigate to the backend folder**
   ```bash
   cd api
   ```
2. **Install composer dependencies**
   ```bash
   ./vendor/bin/sail composer install --ignore-platform-reqs
   ```
3. **Generate application key & public storage link**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan storage:link
   ```
4. **Database migration and seeding**
   ```bash
   ./vendor/bin/sail artisan migrate:refresh --seed
   ```

### 📦 Frontend (Nuxt 3)

1. **Navigate to the frontend folder**
   ```bash
   cd nuxt
   ```
2. **Copy the .env file**
   ```bash
   cp .env.example .env
   ```
3. **Install node dependencies**
   ```bash
   npm install
   ```
4. **Fire up the node application - client & server**
   ```bash
   npm run dev
   ```

---

## ✨ Main Features

### Patient Management

* List, create, update, and delete patient records.
* Live search and filtering by name, gender, and age ranges.
* Data table with pagination, sorting, and server-side loading.
* Integrated `Pinia` store for reactive state management.

### Audio Upload and Transcription

* Upload `.mp3`, `.wav`, and other audio files.
* Real-time loading animations and status indicators.
* Transcription using speech-to-text technology.
* Summarization using LLMs like GPT.

### Global Error Handling

* Client-side fetch interception for consistent error reporting via snackbars.
* Secure backend route handling with token validation.

---

## 🚀 Tech Stack

| Layer       | Tech                           |
| ----------- |--------------------------------|
| Frontend    | Nuxt 3, Vuetify 3, Pinia       |
| API Proxy   | Nuxt server routes             |
| Backend     | PHP (Laravel 12)               |
| External    | MockAPI.io,AssemblyAI,OpenAI   |

---

## 🧱 Architecture

```
Client (Nuxt 3, Vuetify)
│
├── DataTable Store (Pinia)     <----->   Server API Endpoints (Nuxt server routes)
│                                      ↕
├── Uploader.vue                         PHP API Proxy (uses Guzzle to contact MockAPI, AssemblyAI, and OpenAI)
│                                      ↕
├── Error Interceptor (plugin)          External APIs:
│                                        ├── MockAPI (Patient data)
└── Audio Modules                        ├── Speech-to-Text API
                                         └── Language Model API
```

## 🏗 Architecture Overview

### 📥 Backend (Laravel)

#### 🗝️ Authentication

- User login is achieved using the `/api/login` endpoint that returns an **API access token**.
- Logout is possible using the `/api/logout` endpoint that invalidates the current user's request token.

#### 📁 File Upload

- Each chunk is stored in a temporary file under `/storage/app/private/uploads/{uuid}.part`.
- Metadata about the temporary file is stored under `/storage/app/private/uploads/{uuid}.info`.
- Once all expected chunks are received:
    1. The backend reassembles the file in full under `/storage/app/private/uploads/{uuid}`.
    2. The complete file is saved using **Spatie MediaLibrary** in the current user's collection for file management.
    3. Temporary files created are deleted.

#### 🗣️ Voice Transcription (ASR)

- The assembled audio file is passed to an **automatic speech recognition (ASR)** service.
- Transcription is handled via a real ASR API (AssemblyAI)

#### 🧠 Text Summarization (LLM)

- After transcription, the transcript is sent to a **Large Language Model (LLM)** for summarization.
- The summary extracts key ideas for easy review.
- This was implemented using an actual API (OpenAI)

---

### 📤 Frontend (Nuxt 3 + Vuetify)

#### 🗝️ Authentication

- User login is handled via a validated login form.
- Access control is achieved using Nuxt's **route middleware**, blocking unauthorized users from protected routes.

#### 📁 File Upload

- Users upload audio files using the **Uppy** upload component.
- Before upload begins, an HTTP HEAD request checks for resumable uploads via `upload_id`.
- Upload begins with an HTTP POST request, including:
    - `Upload-Length`: Total byte size of the file
    - `Upload-Metadata`: Base64-encoded metadata (filename, mimetype, etc.)
- File is sliced into 1MB chunks and uploaded using HTTP PATCH requests, each with:
    - `uploadId`: unique session ID
    - `Upload-Offset`: running byte offset from the last response
- Upload can be cancelled via an HTTP DELETE request to remove partial data.

#### 📁 File Storage

- Chunks and metadata are stored in Laravel’s local `storage/app/private/uploads` folder.
- Final assembled audio files are saved via **Spatie MediaLibrary** in `storage/app/public`.

#### 🔍 Patient Search / Catalog

- A **mock patient API** provides data for the catalog view.
- Supports filtering/search by:
    - Name
    - Gender
    - Age range (standard ranges like `0–17`, `18–25`, `26–35`, `36–45`, `46–60`, `60+`)
- Paginated results, sortable headers, and debounced search.

#### 🧠 Voice Analysis UI

- Once uploaded, the UI visually steps through:
    1. Transcription in progress – animated loader with text
    2. Transcription complete – full transcript displayed
    3. Summarization in progress – animated loader below transcript
    4. Summary complete – highlighted summary view
- Smooth, polished UI powered by **Vuetify 3**

#### ⚠️ Global Fetch Interception

- All client-side fetch requests are wrapped globally using a Nuxt plugin.
- Non-2xx responses trigger a toast via a **Vuetify snackbar store**
- Errors are parsed (JSON or text) and displayed as red toasts
- Network failures are also caught and handled gracefully

#### 🔁 Server-Side Proxying

- Nuxt server API routes forward requests to the backend, attaching the user’s auth token.
- Ensures token privacy and same-origin security.
- Automatically reconstructs query parameters or request bodies depending on method (`GET`, `POST`, etc.)

---

## ✅ Features Implemented

- ✅ Chunked file upload with **retry** logic and **resumable** uploads
- ✅ Upload **cancellation** capability
- ✅ Cookie-based token user **authentication**
- ✅ **Request validation** on login
- ✅ Server-side request **proxying** between Nuxt and Laravel
- ✅ File storage via **Spatie MediaLibrary**
- ✅ **Access control** using Nuxt route middleware
- ✅ Global **error interception** of fetch requests
- ✅ **Audio-to-text transcription** using real ASR
- ✅ **Transcript summarization** using LLM (mocked or via OpenAI API)
- ✅ Responsive, interactive UI built with **Vuetify 3**
- ✅ **Patient search**, filter and pagination with age/gender criteria

---

## 📸 UI/UX Notes

* Animations on data transitions
* Skeleton loader in `<v-data-table-server>`
* Visual indicators for upload, transcription, and summarization
* Chip-based age/gender filters
* Snackbar notifications for success/error

---

## 📌 Limitations & Solutions

- ❌ Access control vulnerable to **forged token attack**.
    - Solution: To ensure full protection of our frontend views we could make a lightweight API call to a backend endpoint `/api/me` **validating the existing token** before letting the user access the view(s) we wish to protect.
- ❌ **Multiple or simultaneous** file upload.
    - Solution: The **Uppy** library offers additional options to expand our capabilities and implement multiple or even simultaneous file upload.

---

## ✅ Future Enhancements

* Upload multiple files for batch transcription
* Allow patient audio notes to be saved to their profile
* Role-based access controls (Admin, Nurse, Doctor)
* Offline mode or IndexedDB caching

---

## 🧪 Testing

This assignment includes some basic **feature** and **unit tests** to ensure the reliability of the system.

- ✅ **Upload Initialization**
- ✅ **Chunk Writing**
- ✅ **Get Offset**
- ✅ **Delete**
- ✅ **Authentication**

### 🧪 To Run Tests

```bash
    ./vendor/bin/sail artisan test
```