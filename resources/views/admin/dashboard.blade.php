 @include('admin.head')

<div id="app" class="flex-center position-ref full-height">

<div class="logo"><img src="{{URL::asset('/images/logo.png')}}" /></div>

<div class="container">
  <ul class="nav nav-tabs">
    <li class="tab"><a class="active" data-toggle="tab" href="#customers">Customers</a></li>
    <li class="tab"><a data-toggle="tab" href="#pricerules">Price Rules</a></li>
    <li class="tab"><a data-toggle="tab" href="#discounts">Discounts</a></li>
  </ul>

  <div class="tab-content">
    <div id="customers" class="tab-pane fade in show active">
      <h3>Customers</h3>
      <customers-component></customers-component>
    </div>
    <div id="pricerules" class="tab-pane fade">
      <h3>Price Rules</h3>
      <price-rules-component></price-rules-component>
    </div>
    <div id="discounts" class="tab-pane fade">
      <h3>Discounts</h3>
      <discounts-component></discounts-component>
    </div>
  </div>
</div>

    


            
</div>

 @include('admin.foot')