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
              <v-layout wrap>
                <v-flex xs12>
                  <v-text-field v-model="items.locales.en" suffix="EN" label="Enter name"></v-text-field>
                </v-flex>
                <v-flex xs12>
                  <v-text-field v-model="items.locales.ru" suffix="RU" label="Введите наименование"></v-text-field>
                </v-flex>
                <v-flex xs12>
                  <v-text-field v-model="items.locales.uz" suffix="UZ" label="Nomini kiriting"></v-text-field>
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
        <td class="text-xs-left">{{ props.item.locales.en }}</td>
        <td class="text-xs-left">{{ props.item.locales.ru }}</td>
        <td class="text-xs-left">{{ props.item.locales.uz }}</td>
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
        text: "Name in English",
        align: "left",
        sortable: false
      },
      {
        text: "Name in Russian",
        align: "left",
        sortable: false
      },
      {
        text: "Name in Uzbek",
        align: "left",
        sortable: false
      }
    ],
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
    editItem(item) {
      this.editedIndex = this.categories.indexOf(item);
      this.items = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      const index = this.categories.indexOf(item);
      confirm("Are you sure you want to delete this item?") &&
        this.categories.splice(index, 1);
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
        Object.assign(this.categories[this.editedIndex], this.items);
      } else {
        const { locales } = this.items;
        this.$store.dispatch("categories/save", locales);
      }
      this.close();
    }
  }
};
</script>

<style>
</style>
