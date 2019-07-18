<template>
  <v-container fluid>
    <v-layout column class="mb-3">
      <v-card>
        <v-layout>
          <v-avatar size="200" class="mr-4">
            <img :src="imgPath" alt="Avatar">
          </v-avatar>
          <div class="mt-3 text-md-left">
            <h2>{{info.title}}</h2>
            <p>{{info.about}}</p>
            <a target="_blank" :href="'https://t.me/'+info.username">@{{info.username}}</a>
          </div>
        </v-layout>
        <hr class="mb-3">

        <v-layout row wrap>
          <v-flex sm6 md3 xs12 class="mb-3 mr-md-5">
            <v-card height="170">
              <v-img
                height="170"
                :src="getPhoto('01.jpg')"
                gradient="to top right, rgba(100,115,201,.33), rgba(25,32,72,.7)"
              >
                <v-layout
                  align-end
                  fill-height
                  pa-3
                  white--text
                  class="justify-center align-center"
                >
                  <div class="title font-weight-light text-sm-center">
                    <v-icon class="mb-1" x-large dark>account_box</v-icon>

                    <p class="mb-3">Subscribers</p>
                    <p class="display-2">{{subs}}</p>
                  </div>
                </v-layout>
              </v-img>
            </v-card>
          </v-flex>
          <v-flex sm6 md3 xs12 class="mb-3">
            <v-card height="170">
              <v-img
                :src="getPhoto('02.jpg')"
                height="170"
                gradient="to top right, rgba(100,115,201,.33), rgba(25,32,72,.7)"
              >
                <v-layout
                  align-end
                  fill-height
                  pa-3
                  white--text
                  class="justify-center align-center"
                >
                  <div class="title font-weight-light text-sm-center">
                    <v-icon class="mb-1" x-large dark>visibility</v-icon>

                    <p class="mb-3">Daily posts views</p>
                    <p class="display-2">{{posts.dailyReach}}</p>
                  </div>
                </v-layout>
              </v-img>
            </v-card>
          </v-flex>

          <v-flex sm6 md3 xs12 class="mb-3 mr-md-5">
            <v-card height="170">
              <v-img
                :src="getPhoto('04.jpg')"
                height="170"
                gradient="to top right, rgba(100,115,201,.33), rgba(25,32,72,.7)"
              >
                <v-layout
                  align-end
                  fill-height
                  pa-3
                  white--text
                  class="justify-center align-center"
                >
                  <div class="title font-weight-light text-sm-center">
                    <v-icon class="mb-1" x-large dark>assignment_turned_in</v-icon>

                    <p class="mb-3">One post views</p>
                    <p class="display-2">{{posts.avgPostReach}}</p>
                  </div>
                </v-layout>
              </v-img>
            </v-card>
          </v-flex>
          <v-flex sm6 md3 xs12 class="mb-3 mr-md-5">
            <v-card height="170">
              <v-img
                :src="getPhoto('05.jpg')"
                height="170"
                gradient="to top right, rgba(100,115,201,.33), rgba(25,32,72,.7)"
              >
                <v-layout
                  align-end
                  fill-height
                  pa-3
                  white--text
                  class="justify-center align-center"
                >
                  <div class="title font-weight-light text-sm-center">
                    <v-icon class="mb-1" x-large dark>autorenew</v-icon>

                    <p class="mb-3">Activity</p>
                    <p class="display-2">{{posts.getErr}}%</p>
                  </div>
                </v-layout>
              </v-img>
            </v-card>
          </v-flex>
        </v-layout>
      </v-card>
    </v-layout>
    <v-card>
      <div class="pa-3">
        <h3>Channel subscribers</h3>
        <br>
        <line-chart :data="members"></line-chart>
      </div>
    </v-card>
  </v-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  data: () => ({
    loading: false
  }),
  created() {
    this.$store.dispatch("stats/load");
    console.log(this);
  },
  methods: {
    getPhoto(name) {
      return Laravel.siteUrl + "/photos/" + name;
    }
  },
  computed: {
    ...mapGetters({
      members: "stats/members",
      posts: "stats/posts",
      info: "stats/info"
    }),
    imgPath: {
      get() {
        return Laravel.siteUrl + "/photos/" + this.info.photo;
      }
    },
    subs: {
      get() {
        const subscribers = Object.values(this.members);
        return subscribers[0];
      }
    }
  }
};
</script>

<style>
</style>
