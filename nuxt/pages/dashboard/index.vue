<template>
    <v-container class="mt-8" fluid>
        <v-row justify="center" class="text-center">
            <v-col cols="12" md="10">
                <v-card elevation="0" class="pa-6">
                    <v-card-title class="text-h4 font-weight-bold text-indigo-darken-2">
                        Healthcare Intelligence Dashboard
                    </v-card-title>

                    <v-card-subtitle class="text-subtitle-1 mb-6">
                        A unified interface for patient data management and voice-based clinical summaries.
                    </v-card-subtitle>

                    <v-divider class="mb-6" />

                    <v-row>
                        <v-col cols="12" md="6">
                            <v-card class="pa-4" outlined>
                                <v-card-title class="text-h6 mb-2">
                                    <v-icon color="primary" start>mdi-account-multiple</v-icon>
                                    Patient Catalog
                                </v-card-title>
                                <v-card-text>
                                    <ul class="pl-4">
                                        <li>🔍 Search and filter patients by name, age, disease, category, and medication</li>
                                        <li>📝 Add new patients using a structured intake form</li>
                                        <li>📋 Display patient records in a clean, readable format</li>
                                        <li>⚡ Fast and responsive UI using Vue</li>
                                    </ul>
                                </v-card-text>
                            </v-card>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-card class="pa-4" outlined>
                                <v-card-title class="text-h6 mb-2">
                                    <v-icon color="purple" start>mdi-microphone</v-icon>
                                    Audio Transcription & Summarization
                                </v-card-title>
                                <v-card-text>
                                    <ul class="pl-4">
                                        <li>🎙️ Upload audio files (MP3/WAV) with a click</li>
                                        <li>🧠 Simulated ASR system transcribes medical notes</li>
                                        <li>📝 AI (mocked or real) generates a concise summary</li>
                                        <li>✅ Real-time feedback with loading and error handling</li>
                                    </ul>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-divider class="my-6" />

                    <v-card class="pa-4" color="blue-darken-5" variant="tonal">
                        <v-card-title class="text-h6 mb-2">
                            <v-icon color="blue" start>mdi-lightbulb-on-outline</v-icon>
                            Why This Matters
                        </v-card-title>
                        <v-card-text>
                            This dashboard brings together structured data and smart voice tools to reduce paperwork,
                            streamline care, and support faster, AI-assisted decision-making in healthcare environments.
                        </v-card-text>
                    </v-card>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
</script>
