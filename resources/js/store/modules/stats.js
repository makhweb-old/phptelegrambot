import axios from "axios";
import { api } from "~/config";
import * as types from "../mutation-types";

/**
 * Initial state
 */
export const state = {
  data: {
    members: {},
    posts: {},
    info: {}
  }
};

/**
 * Mutations
 */
export const mutations = {
  [types.SET_STATS_DATA](state, payload) {
    state.data = payload;
  }
};

/**
 * Actions
 */
export const actions = {
  async load({ commit }) {
    const { data } = await axios.get(api.path("stats"));
    commit(types.SET_STATS_DATA, data);
  }
};

/**
 * Getters
 */
export const getters = {
  members: state => state.data.members,
  posts: state => state.data.posts,
  info: state => state.data.info
};
