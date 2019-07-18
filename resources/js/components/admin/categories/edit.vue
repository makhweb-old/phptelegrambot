<template>
  <v-layout>
    <v-flex xs12 sm8 offset-sm2 offset-xs0>
      <v-card>
        <v-progress-linear indeterminate color="primary darken-2" v-if="!loaded"></v-progress-linear>
        <div class="pa-4" v-if="loaded" >
          <div>
            <h3 class="headline pb-3">{{items.locales.ru}}</h3>
          </div>
          <v-form ref="form" lazy-validation>
            <v-text-field
              v-for="(item,key) in items.translations"
              v-model="item.name"
              :counter="30"
              :label="'Name in '+locales[item.lang]"
              :key="key"
              required
            ></v-text-field>
            <v-layout row wrap>
              <v-flex xs12 md4 v-for="(item) in items.products" :key="item.id * 999">
                <v-card class="mr-3">
                  <v-img :src="item.photo" height="300px">
                    <v-layout column fill-height>
                      <v-spacer></v-spacer>
                      <v-card-title class="white--text pl-5 pt-5">
                        <div class="display-1 pl-5 pt-5">{{item.name}}</div>
                      </v-card-title>
                    </v-layout>
                  </v-img>
                  <v-list>
                    <v-form @click>
                      <v-expansion-panel>
                        <v-expansion-panel-content
                          v-for="(translation,key) in item.translations"
                          :key="key"
                        >
                          <template v-slot:header>
                            <div>{{locales[translation.lang]}}</div>
                          </template>
                          <v-card class="pa-3">
                            <v-text-field v-model="translation.name" :label="'Name'" required></v-text-field>

                            <v-textarea
                              outlined
                              label="Description"
                              v-model="translation.description"
                            ></v-textarea>
                          </v-card>
                        </v-expansion-panel-content>
                      </v-expansion-panel>

                      <v-divider></v-divider>
                      <v-divider></v-divider>
                      <div class="px-3 mb-3">
                        <UploadImage v-model="item.photo"/>
                        <v-text-field
                          v-model="item.price"
                          suffix="UZS"
                          prepend-icon="attach_money"
                          label="Price"
                          required
                        ></v-text-field>
                      </div>
                    </v-form>
                  </v-list>
                  <v-btn block color="warning" @click="deleteProduct(item.id)">Delete</v-btn>
                </v-card>
              </v-flex>
            </v-layout>
          </v-form>
          <v-btn block color="primary" @click="openModal">Add new</v-btn>
          <v-btn block color="success" @click="update">Save</v-btn>
        </div>
      </v-card>
    </v-flex>
    <v-layout row justify-center>
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            <span class="headline">Product</span>
          </v-card-title>

          <v-container grid-list-md>
            <v-layout wrap>
              <v-flex md12>
                <v-img v-if="product.photo" class="white--text" :src="product.photo"/>
                <UploadImage v-model="product.photo"/>
                <v-text-field
                  label="Price"
                  class="pt-0"
                  suffix="UZS"
                  prepend-icon="attach_money"
                  v-model="product.price"
                  persistent-hint
                  required
                ></v-text-field>
              </v-flex>

              <v-flex v-for="(item,key) in product.translations" :key="key" xs12 sm6 md4>
                <b>{{locales[item.lang]}}</b>
                <v-text-field v-model="item.name" :label="'Name'" required></v-text-field>

                <v-textarea outlined label="Description" v-model="item.description"></v-textarea>
              </v-flex>
            </v-layout>
          </v-container>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="dialog = false">Close</v-btn>
            <v-btn color="blue darken-1" flat @click="saveProduct">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-layout>
  </v-layout>
</template>
<script>
import UploadImage from "../../ui/UploadImage";
export default {
  data: () => ({
    items: [],
    checkbox: false,
    dialog: false,
    product: [],
    loaded:false,
    defaultProduct: {
      translations: [
        {
          lang: "uz",
          name: null,
          description: null
        },
        {
          lang: "ru",
          name: null,
          description: null
        },
        {
          lang: "en",
          name: null,
          description: null
        }
      ],
      price: null,
      photo: null
    }
  }),

  components: {
    UploadImage
  },

  created() {
    this.defaultProduct.category_id = this.id;
    this.fetch();
  },

  methods: {
    async fetch() {
      await this.$store.dispatch("categories/fetchOne", this.id);
      this.items = this.$store.getters["categories/item"];
      this.loaded = true;
    },
    update() {
      this.$store.dispatch("categories/update", this.items).then(() => {
        alert("Success!");
        this.fetch();
      });
    },
    openModal() {
      this.product = JSON.parse(JSON.stringify(this.defaultProduct));
      this.dialog = true;
    },
    imgPath(file) {
      return Laravel.siteUrl + "/photos/" + file;
    },
    deleteProduct(id) {
      if (confirm("Are you sure?")) {
        this.$store.dispatch("products/delete", id).then(() => {
          this.fetch();
        });
      }
    },
    saveProduct() {
      this.$store.dispatch("products/save", this.product).then(() => {
        this.dialog = false;
        this.fetch();
      });
    }
  },
  computed: {
    id: {
      get() {
        return this.$route.params.id;
      }
    },
    locales: {
      get() {
        return {
          ru: "Russian",
          en: "English",
          uz: "Uzbek"
        };
      }
    }
  }
};
</script>

<style scoped>
.v-expansion-panel {
  box-shadow: none;
}
</style>
