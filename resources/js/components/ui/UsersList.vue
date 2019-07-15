<template>
  <v-card>
    <v-toolbar flat color="white">
      <v-toolbar-title>Users</v-toolbar-title>
    </v-toolbar>
    <v-data-table :headers="headers" :items="items" :loading="tableLoading" class="elevation-1">
      <template slot="headerCell" slot-scope="props">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <span v-on="on">{{ props.header.text }}</span>
          </template>
          <span>{{ props.header.text }}</span>
        </v-tooltip>
      </template>
      <template v-slot:items="props">
        <td>{{ props.item.first_name }}</td>
        <td class="text-xs-left">{{ props.item.last_name }}</td>
        <td class="text-xs-left">{{ props.item.username }}</td>
        <td class="text-xs-left">{{ props.item.selected_language }}</td>
        <td class="text-xs-left">{{ props.item.phone_number }}</td>
        <td class="text-xs-left">{{ props.item.created_at }}</td>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import axios from "axios";
import { api } from "~/config";
export default {
  data: () => ({
    tableLoading: true,
    headers: [
      {
        text: "First name",
        align: "left",
        sortable: false,
        value: "first_name"
      },
      { text: "Last name", value: "last_name", align: "left" },
      { text: "Username", value: "username", align: "left" },
      { text: "Language", value: "selected_language", align: "left" },
      { text: "Phone number", value: "phone_number", align: "left" },
      { text: "Created at", value: "created_at", align: "left" }
    ],
    items: []
  }),
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios.post(api.path("users")).then(response => {
        this.items = response.data;
        this.tableLoading = false;
      });
    }
  }
};
</script>

<style>
</style>
