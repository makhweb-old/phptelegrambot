<template>
  <div>
    <v-card>
      <div class="pa-4">
        <v-toolbar flat color="white">
          <v-toolbar-title>Send Message:</v-toolbar-title>
        </v-toolbar>
        <v-form ref="form">
          <v-textarea box v-model="text" :counter="250" label="Text" required></v-textarea>
          <v-checkbox color="primary" v-model="sendTo.users" label="Users"></v-checkbox>
          <v-checkbox color="primary" v-model="sendTo.channels" label="Channel"></v-checkbox>
          <v-btn @click="submit">submit</v-btn>
        </v-form>
      </div>
      <v-progress-linear :indeterminate="true" v-show="loading"></v-progress-linear>
    </v-card>
    <v-snackbar v-model="snackbar">
      {{ 'Successfully delivered to '+snackbarData.delivered + ' chats ✔️'}}
      <v-btn color="pink" flat @click="snackbar = false">Close</v-btn>
    </v-snackbar>
  </div>
</template>

<script>
import axios from "axios";
import { api } from "~/config";
export default {
  data: () => ({
    text: "",
    snackbar: false,
    snackbarData: [],
    loading: false,
    sendTo: {
      users: true,
      channels: false
    }
  }),
  methods: {
    submit() {
      if (this.text) {
        this.loading = true;
        const { text, sendTo } = this;
        axios.post(api.path("sendMessage"), { text, sendTo }).then(response => {
          this.text = null;
          this.snackbar = true;
          this.loading = false;
          this.snackbarData = response.data;
        });
      }
    }
  }
};
</script>

<style scoped>
.v-input--selection-controls {
  margin-top: 0;
  height: 30px;
}
</style>
