<template>
  <v-layout>
    <v-flex xs12 sm8 offset-sm3>
      <v-card>
        <div class="pa-4">
          <v-card-title class="pl-0" primary-title>
            <div>
              <h3 class="headline mb-0">Category {{id}}</h3>
            </div>
          </v-card-title>
          <v-form ref="form" lazy-validation>
            <v-text-field
              v-for="(item,key) in items.translations"
              v-model="item.name"
              :counter="30"
              :label="'Name in '+locales[item.lang]"
              :key="key"
              required
            ></v-text-field>
            <v-flex sm4 v-for="(item) in items.products" :key="item.id">
              <v-card>
                <v-img :src="imgPath + item.photo" height="300px">
                  <v-layout column fill-height>
                    <v-spacer></v-spacer>
                    <v-card-title class="white--text pl-5 pt-5">
                      <div class="display-1 pl-5 pt-5">{{item.name}}</div>
                    </v-card-title>
                  </v-layout>
                </v-img>
                <v-list>
                  <v-form class="px-3" @click>
                    <v-text-field
                      v-for="(translation,key) in item.translations"
                      :key="key"
                      v-model="translation.name"
                      :label="'Name in '+locales[translation.lang]"
                      required
                    ></v-text-field>
                    <v-divider></v-divider>
                    <v-text-field
                      v-model="item.price"
                      label="Price"
                      required
                    ></v-text-field>
                  </v-form>
                </v-list>
              </v-card>
            </v-flex>
          </v-form>
        </div>
      </v-card>
    </v-flex>
  </v-layout>
</template>
<script>
export default {
  data: () => ({
    name: "",
    email: "",
    select: null,
    items: [],
    checkbox: false
  }),

  created() {
    this.fetch();
  },

  methods: {
    async fetch() {
      await this.$store.dispatch("categories/fetchOne", this.id);
      this.items = this.$store.getters["categories/item"];
    }
  },
  computed: {
    id: {
      get() {
        return this.$route.params.id;
      }
    },
    imgPath: {
      get() {
        return Laravel.siteUrl + "/photos/";
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