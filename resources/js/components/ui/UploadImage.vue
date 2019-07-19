<template>
  <div>
    <v-text-field
      label="Select Image"
      @click="pickFile"
      prepend-icon="attach_file"
      readonly
    ></v-text-field>
    <v-progress-linear indeterminate color="primary darken-2" v-if="loading"></v-progress-linear>
    <input type="file" style="display: none" ref="image" accept="image/*" @change="onFilePicked">
  </div>
</template>

<script>
import axios from "axios";
import { api } from "~/config";

export default {
  props: ["value"],
  data: () => ({
    title: "Image Upload",
    dialog: false,
    imageName: "",
    imageUrl: "",
    imageFile: "",
    loading: false
  }),
  computed: {
    filter: {
      get() {
        if (this.value) {
          const paths = this.value.split("/");
          return paths[paths.length - 1];
        }
      }
    }
  },
  methods: {
    pickFile() {
      this.$refs.image.click();
    },

    upload() {
      const formData = new FormData();
      formData.append("file", this.imageFile);

      axios
        .post(api.path("upload"), formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(({ data }) => {
          this.$emit("input", data.filename);
          this.loading = false;
        });
    },

    onFilePicked(e) {
      const files = e.target.files;
      if (files[0] !== undefined) {
        this.imageName = files[0].name;
        if (this.imageName.lastIndexOf(".") <= 0) {
          return;
        }
        this.loading = true;
        const fr = new FileReader();
        fr.readAsDataURL(files[0]);
        fr.addEventListener("load", () => {
          this.imageUrl = fr.result;
          this.imageFile = files[0]; // this is an image file that can be sent to server...
          this.upload();
        });
      } else {
        this.imageName = "";
        this.imageFile = "";
        this.imageUrl = "";
      }
    }
  }
};
</script>

<style>
</style>
