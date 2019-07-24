<template>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-1">
             <button class="btn btn-primary" v-on:click="syncPriceRules">Sync</button>
             <div><label for="title">Title</label><input name="title" placeholder="Price Rule Title" v-model="title" /></div>
             <div><label for="target_type">Target Type</label><select name="target_type" v-model="target_type">
                 <option value="line_item">Line Item</option>
                 <option value="shipping_line">Shipping Line</option>
             </select></div>
             <div><label for="allocation_method">Allocation Method</label><select name="allocation_method" v-model="allocation_method">                
                 <option value="across">Across</option>
                 <option value="each">Each</option>
             </select></div>
             <div><label for="value_type">Value Type</label><select name="value_type" v-model="value_type">
                 <option value="fixed_amount">Fixed Amount</option>
                 <option value="percentage">Percentage</option>
             </select></div>
             <div><label for="value">Value</label><input name="value" placeholder="Value" v-model="value" /></div>
             <div><button class="btn btn-secondary" v-on:click="addPriceRule">Add</button></div>

            </div>
            <div class="col-md-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Value Type</th>
                            <th scope="col">Value</th>
                        </tr>
                      </thead>
                        <tbody>
                            <tr v-for="price_rule in price_rules">
                                <td>{{price_rule.price_rule_id}}</td>
                                <td>{{price_rule.title}}</td>
                                <td>{{price_rule.value_type}}</td>
                                <td>{{price_rule.value}}</td>
                               <!--  <td><button v-on:click="deletePriceRule(price_rule.price_rule_id)">delete</button></td> -->
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
                price_rules: [],
                discount_codes: [],
                title: '',
                target_type: '',
                allocation_method: '',
                value_type: '',
                value: '',
                
            }
        },
        methods:
        {
            getPriceRules: function()
            {
                let self = this;
                axios.get('/api/pricerules')
                  .then(function (response) {
                    self.price_rules = response.data;
                    //console.log(response);
                  })
                  .catch(function (error) {
                    // handle error
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            },
            addPriceRule: function()
            {
                let self = this;
                axios.post('/api/pricerules',
                {
                    price_rules: self.price_rules,
                    discount_codes: self.discount_codes,
                    title: self.title,
                    target_type: self.target_type,
                    allocation_method: self.allocation_method,
                    value_type: self.value_type,
                    value: self.value
                })
                  .then(function (response) {
                     Swal.fire({
                      type: 'success',
                      title: 'Price Rule',
                      text: 'Price Rule Added',
                    });
                    console.log(response);
                  })
                  .catch(function (error) {
                     Swal.fire({
                      type: 'error',
                      title: 'Price Rule',
                      text: 'Error adding Price Rule. Please check documentation to ensure the options combinations are valid',
                    });
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            },
            syncPriceRules: function()
            {
                let self = this;
                axios.get('/api/pricerules/import')
                  .then(function (response) {
                    self.getPriceRules();
                  })
                  .catch(function (error) {
                    // handle error
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            },
            deletePriceRule: function(pr_id)
            {
                let self = this;
                axios.delete('/api/pricerules/'.pr_id,{'price_rule_id': pr_id})
                  .then(function (response) {
                    //self.getPriceRules();
                    console.log(response);
                  })
                  .catch(function (error) {
                    // handle error
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            }
        },
        mounted()
        {

            this.getPriceRules();

        }
    }
</script>

<style>
label{
    display: inline-block;
    min-width: 150px;
}
</style>
