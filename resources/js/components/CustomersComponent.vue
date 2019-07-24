<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
              <div class="button-container">
                <button class="btn btn-primary" v-on:click="syncCustomers">Sync</button>
                <button class="btn btn-primary" v-on:click="topCustomers">Top Customers</button>
                <button class="btn btn-primary" v-on:click="emailSelected">{{generateCodeLabel}}</button>
              </div>
                <table id="customerTable" class="table table-striped">
                    <thead>
                        <tr>
                          <th scope="col"><!-- <input v-model="selectAll" @click="select" type="checkbox"> --></th>
                          <th scope="col">Email</th>
                          <th scope="col">Name</th>
                          <th scope="col">Total Spent</th>
                          <th scope="col">Accepts Marketing</th>
                          <th scope="col">Last Synced</th>
                        </tr>
                      </thead>
                          <tbody>
                            <tr v-for="customer in customers">
                                <td><!-- <input v-model="selected" :value="customer.id" type="checkbox"> --></td>
                                <td>{{ customer.email }}</td>
                                <td>{{ customer.first_name }} {{ customer.last_name }}</td>
                                <td>{{ customer.total_spent }}</td>
                                <td>
                                    <span v-if="customer.accepts_marketing">Yes</span>
                                    <span v-else="customer.accepts_marketing">No</span>
                                </td>
                                <td>{{ customer.updated_at }}</td>
                          </tr>
                      </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                customers: [],
                selected: [],
                email_list: [],
                selectAll: false,
                generateCodeLabel: 'Generate and Email codes'
            }
        },
        methods:
        {
          select: function()
          {
              this.selected = [];
              if (!this.selectAll) {
                for (let i in this.customers) {
                  this.selected.push(this.customers[i].id);
                }
              }
            },
            getCustomers: function()
            {
                let self = this;
                axios.get('/api/customers')
                  .then(function (response) {
                    // handle success
                    self.customers = response.data;

                     
                       
                  })
                  .catch(function (error) {
                    // handle error
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
              },
            topCustomers: function()
            {
                let self = this;
                axios.get('/api/customers/top-spending')
                  .then(function (response) {
                    // handle success
                    self.customers = response.data.customers;
                    console.log(response);
                  })
                  .catch(function (error) {
                    Swal.fire({
                      type: 'error',
                      title: 'Customers',
                      text: 'Error retrieving top customers',
                    });
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
              },
            syncCustomers: function()
            {
                let self = this;
                axios.get('api/customers/import')
                  .then(function (response) {
                    // handle success
                    //once re-synced, re-load customers from Database
                    self.getCustomers();

                     Swal.fire({
                      type: 'success',
                      title: 'Customer Sync',
                      text: 'Customers have been synced',
                    });

                  })
                  .catch(function (error) {
                     Swal.fire({
                      type: 'error',
                      title: 'Customer Sync',
                      text: 'Error Syncing Customers',
                    });
                     console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            },
            emailSelected: function()
            {
              this.generateCodeLabel = 'Please wait...';
              let self = this;
                axios.post('api/discounts/create-customer-codes',
                  {
                    customers: self.customers
                  })
                  .then(function (response) {
                    Swal.fire({
                      type: 'success',
                      title: 'Discount Codes',
                      text: 'Customers have been emailed their codes',
                    });
                    console.log(response);
                  })
                  .catch(function (error) {
                    Swal.fire({
                      type: 'error',
                      title: 'Discount Codes',
                      text: 'Customers not emailed',
                    });
                    console.log(error);
                  })
                  .finally(function () {
                    self.generateCodeLabel = 'Generate and Email codes'
                  });
            }
        },
        mounted()
        {
            this.getCustomers();

        }
    }


</script>
