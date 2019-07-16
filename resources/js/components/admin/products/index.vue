<template>
  <div>
    <v-toolbar flat color="white">
      <v-toolbar-title>My CRUD</v-toolbar-title>
      <v-divider class="mx-2" inset vertical></v-divider>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" max-width="500px">
        <template v-slot:activator="{ on }">
          <v-btn color="primary" dark class="mb-2" v-on="on">New Item</v-btn>
        </template>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap v-if="isNew">
                <v-flex xs12>
                  <v-text-field v-model="items.locales.en" suffix="UZ" label="Enter name"></v-text-field>
                </v-flex>
                <v-flex xs12>
                  <v-text-field v-model="items.locales.ru" suffix="RU" label="Введите наименование"></v-text-field>
                </v-flex>
                <v-flex xs12>
                  <v-text-field v-model="items.locales.uz" suffix="EN" label="Nomini kiriting"></v-text-field>
                </v-flex>
              </v-layout>
              <v-layout wrap v-else>
                <v-flex xs12 v-for="(item,key) in editedItem.translations" :key="key">
                  <v-text-field v-model="item.name" :suffix="toUpper(item.lang)" label="Enter name"></v-text-field>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click="save">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-toolbar>
    <v-data-table :headers="headers" :items="categories" class="elevation-1">
      <template v-slot:items="props">
        <td
          class="text-xs-left"
          v-for="(translation,index) in props.item.translations"
          :key="index"
        >{{ translation.name }}</td>
        <td class="justify-center layout px-0">
          <v-icon small class="mr-2" @click="editItem(props.item)">edit</v-icon>
          <v-icon small @click="deleteItem(props.item)">delete</v-icon>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from "axios";
import { api } from "~/config";
import { mapGetters } from "vuex";

export default {
  data: () => ({
    dialog: false,
    headers: [
      {
        text: "Name in Uzbek",
        align: "left",
        sortable: false
      },
      {
        text: "Name in Russian",
        align: "left",
        sortable: false
      },
      {
        text: "Name in English",
        align: "left",
        sortable: false
      }
    ],
    editedItem: [],
    editedIndex: -1,
    items: {
      locales: {
        uz: null,
        ru: null,
        en: null
      }
    },
    defaultItem: {
      locales: {
        uz: null,
        ru: null,
        en: null
      }
    }
  }),

  mounted() {
    this.$store.dispatch("categories/fetch");
  },

  computed: {
    isNew() {
      return this.editedIndex === -1;
    },
    formTitle() {
      return this.isNew ? "New Item" : "Edit Item";
    },
    ...mapGetters({
      categories: "categories/data"
    })
  },

  watch: {
    dialog(val) {
      val || this.close();
    }
  },

  methods: {
    toUpper(text) {
      return text.toUpperCase();
    },
    editItem(item) {
      this.editedIndex = this.categories.indexOf(item);

      // We need copy deep object
      // (because when changes editedItem will change categories too)
      this.editedItem = JSON.parse(JSON.stringify(item));
      this.dialog = true;
    },

    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$store.dispatch("categories/delete", item.id);
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.items = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      if (this.editedIndex > -1) {
        this.$store.dispatch("categories/update", this.editedItem);
      } else {
        this.$store.dispatch("categories/save", this.items.locales);
      }
      this.close();
    }
  }
};
</script>

<style>
</style>
