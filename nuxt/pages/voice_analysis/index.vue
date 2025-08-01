<template>
    <v-stepper
        v-model="stepModel"
        :items="items"
        color="primary"
        show-actions
    >
        <template v-slot:item.1>
            <h3 class="text-h6">Audio file upload</h3>
            <br>
            Select and upload a high-quality audio file containing your recorded voice.
            This file will be processed in the next steps to generate an accurate transcription
            and a concise summary.
            <br>
            <Uploader @uploaded="onUploaded" @reset="disabledNext = true" />
        </template>

        <template v-slot:item.2>
            <h3 class="text-h6">Voice transcription</h3>
            <br>
            Now that your audio file is uploaded, our system is automatically transcribing
            the speech into written text using advanced speech recognition technology.
            This transcription will appear below once ready.
            <div class="voice-loader" v-if="transcriptionLoading">
                <span class="bar" v-for="n in 25" :key="n" :style="{ animationDelay: `${n * 0.1}s` }"></span>
            </div>
            <div v-else-if="!transcriptionLoading && !items[1]?.hasError">
                <h4 class="text-subtitle-1 mt-5 mb-2 text-center">📄 Transcribed Text 📄</h4>
                <div class="transcription-box">
                    {{ transcriptionText }}
                </div>
            </div>
        </template>

        <template v-slot:item.3>
            <h3 class="text-h6">Transcript summarization</h3>
            <br>
            The final step will generate a summarized version of the transcription,
            extracting the key points and main ideas for a quick overview.
            <div class="mt-6">
                <v-skeleton-loader v-if="summaryLoading" type="list-item-three-line" />
                <div v-else-if="!summaryLoading && !items[2]?.hasError">
                    <h4 class="text-subtitle-1 mb-5 mb-2 text-center">🧠 Summary 🧠</h4>
                    <div class="summary-box">
                        {{ summaryText }}
                    </div>
                </div>
            </div>
        </template>

        <template v-slot:prev>
            <v-btn
                v-if="stepModel !== 1"
                :disabled="!items?.filter(i => i.hasError)?.length"
                :text="'Back'"
                @click="onPrev"
            />
            <div v-else></div>
        </template>
        <template v-slot:next>
            <v-btn
                :disabled="disabledNext"
                :color="stepModel === 3 ? 'primary' : ''"
                :text="nextButtonTexts[stepModel - 1]"
                @click="onNext"
            />
        </template>
    </v-stepper>
</template>
<script setup>
import Uploader from "~/components/Uploader.vue";

const stepModel = ref(0);
const disabledNext = ref(true);
const mediaId = ref(null);
const transcriptionLoading = ref(false);
const transcriptionText = ref('');
const summaryLoading = ref(false);
const summaryText = ref('');
const items = reactive([
    { title: 'Upload', hasError: false },
    { title: 'Transcribe', hasError: false },
    { title: 'Summarize', hasError: false },
])
const nextButtonTexts = reactive([
    'Transcribe',
    'Summarize',
    'Reset',
]);

const onUploaded = (id) => {
    mediaId.value = id;
    disabledNext.value = false;
}
const onPrev = () => {
    stepModel.value = stepModel.value - 1;
    items.forEach((item, index) => items[index] = {...item, hasError: false});
    disabledNext.value = false;
}
const onNext = () => {
    if(stepModel.value === 1) transcribe()
    if(stepModel.value === 2) summarize()
    stepModel.value === 3 ? (stepModel.value = 1) : stepModel.value++;
    disabledNext.value = true;
}
function transcribe(){
    transcriptionLoading.value = true;
    fetch(`/api/voice_analysis?path=transcribe`, {
        method: 'POST',
        body: JSON.stringify({mediaId: mediaId.value}),
    })
        .then(async r => {
            const res = await r.json();
            if(res.error)
                items[1].hasError = true;
             else {
                transcriptionText.value = res?.transcript;
                disabledNext.value = false;
            }
        })
        .finally(() => transcriptionLoading.value = false)
}
function summarize(){
    summaryLoading.value = true;
    fetch(`/api/voice_analysis?path=summarize`, {
        method: 'POST',
        body: JSON.stringify({text: transcriptionText.value}),
    })
        .then(async r => {
            const res = await r.json();
            if(res.error)
                items[2].hasError = true;
            else {
                summaryText.value = res?.summary;
                disabledNext.value = false;
            }
        })
        .finally(() => summaryLoading.value = false)
}
</script>
<style scoped>
.voice-loader {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    height: 40px;
    margin-top: 30px;
    gap: 5px;
}

.voice-loader .bar {
    width: 6px;
    height: 100%;
    background: #3f51b5;
    border-radius: 4px;
    animation: bounce 1.2s infinite ease-in-out;
}

.transcription-box {
    background-color: #f5f5f5;
    border-left: 4px solid #3f51b5;
    padding: 16px;
    margin-top: 20px;
    border-radius: 8px;
    font-size: 1rem;
    line-height: 1.5;
    color: #333;
    animation: fadeIn 0.4s ease-in;
    white-space: pre-wrap;
}
.summary-box {
    background-color: #e3f2fd;
    border-left: 4px solid #1976d2;
    padding: 16px;
    border-radius: 8px;
    font-size: 1rem;
    line-height: 1.6;
    color: #0d47a1;
    animation: fadeIn 0.4s ease-in;
    white-space: pre-wrap;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
@keyframes bounce {
    0%, 100% {
        height: 10px;
    }
    50% {
        height: 35px;
    }
}
</style>