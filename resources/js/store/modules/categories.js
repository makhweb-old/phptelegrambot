import axios from "axios";
import { api } from "~/config";
import * as types from "../mutation-types";

/**
 * Initial state
 */
export const state = {
  data: [],
  item: []
};

/**
 * Mutations
 */
export const mutations = {
  [types.SET_CATEGORY_DATA](state, payload) {
    state.data = payload;
  },
  [types.SET_CATEGORY_ITEM](state, payload) {
    state.item = payload;
  }
};

/**
 * Actions
 */
export const actions = {
  async fetch({ commit }) {
    const { data } = await axios.get(api.path("categories"));
    commit(types.SET_CATEGORY_DATA, data);
  },
  async fetchOne({ commit }, id) {
    const { data } = await axios.get(`${api.path("categories")}/${id}`);
    commit(types.SET_CATEGORY_ITEM, data);
  },
  async save({ dispatch }, payload) {
    await axios.post(api.path("categories"), {
      data: payload
    });
    dispatch("fetch");
  },
  async delete({ dispatch }, payload) {
    await axios.delete(`${api.path("categories")}/${payload}`);
    dispatch("fetch");
  },
  async update({ dispatch }, payload) {
    await axios.patch(`${api.path("categories")}/${payload.id}`, {
      data: payload
    });
    dispatch("fetch");
  }
};

/**
 * Getters
 */
export const getters = {
  data: state => state.data,
  item: state => state.item
};
