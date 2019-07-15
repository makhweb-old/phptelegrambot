<template>
  <v-container grid-list-md text-xs-center>
    <v-layout justify-center row wrap>
      <v-flex xs12>
        <SendMessage/>
      </v-flex>
      <v-flex xs12>
        <UsersList/>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import SendMessage from '../../ui/SendMessage'
import UsersList from '../../ui/UsersList'
export default {
  components:{
    SendMessage,UsersList
  }
};
</script>
