<template>
  <div>
    <section class="hero text-center bg-secondary mb-4 text-white">
      <div class="container">
        <h1 class="font-28 fw-600 text-uppercase">
          Update Design Information
        </h1>
      </div>
    </section>


  </div>
</template>

<script>
export default {
  middleware: ['auth'],
  data() {
    return {
      form: this.$vform({
        title: '',
        description: '',
        is_live: false,
        tags: [],
        assign_to_team: false,
        team: null
      })
    };
  },

  async asyncData({ $axios, params, error, redirect }) {
    try {
      const design = await $axios.$get(`/designs/${params.id}`);

      return { design: design.data };
    } catch (err) {
      if (err.response.status === 404) {
        error({ statusCode: 404, message: 'Design not found' });
      } else if (err.response.status === 401) {
        redirect('/login');
      } else {
        error({ statusCode: 500, message: 'Internal server error' });
      }
    }
  },

  methods: {
    submit() {

    }
  },

  mounted() {
    if (this.design) {
      Object.keys(this.form).forEach(key => {
        if (this.design.hasOwnProperty(key)) {
          this.form[key] = this.design[key];
        }
      });
      this.form.tags = this.design.tag_list.tags || [];

      if (this.design.team) {
        this.form.team = this.design.team.id;
        this.form.assign_to_team = true;
      } else {
        this.form.assign_to_team = false;
      }
    }
  }
};
</script>

<style>

</style>
