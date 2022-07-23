@extends("website.{$locale}.layouts.master")

@section('content')
    <div class="mid-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="inner-heading">Categories of rental goods</h3>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/002-plot.svg') }}">
                        </div>
                        <p class="app-name">Real Estate</p>
                        <p class="app-description">(Villas and apartments, storages, lands, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/001-heavy-machinery.svg') }}">
                        </div>
                        <p class="app-name">Heavy Equipment</p>
                        <p class="app-description">(construction containers, forklift, shovel, and caterpillar, and more.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/003-combine-harvester.svg') }}">
                        </div>
                        <p class="app-name">Agricultural Equipment</p>
                        <p class="app-description">(tillers, harvesting and fertilization machines, and more.)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/004-vehicle.svg') }}">
                        </div>
                        <p class="app-name">Outdoor Cleaning Equipment</p>
                        <p class="app-description">(Bulldozer, industrial vacuum, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/005-drill.svg') }}">
                        </div>
                        <p class="app-name">Electrical and Plumbing Equipment</p>
                        <p class="app-description">(drill, pipe cutting machine, ladder, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/006-treadmill-machine.svg') }}">
                        </div>
                        <p class="app-name">Sport Equipment and Supplies</p>
                        <p class="app-description">(running machine, bikes, dumbbell, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/007-wheelchair.svg') }}">
                        </div>
                        <p class="app-name">Medical Equipment and 
                            Elderly Supplies</p>
                        <p class="app-description">(medical bed, wheelchair, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/008-tennis-racket.svg') }}">
                        </div>
                        <p class="app-name">Adults’ Games and Children’s 
                            Games</p>
                        <p class="app-description">(Tennis and Billiards tables, bouncing-game, soapy-game, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/009-game-console.svg') }}">
                        </div>
                        <p class="app-name">Computers, Video Games, 
                            and Electronics. </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/010-book.svg') }}">
                        </div>
                        <p class="app-name">Books</p>
                        <p class="app-description">(textbooks for colleges, children’s books, novels, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/011-furniture.svg') }}">
                        </div>
                        <p class="app-name">Office and Home Furniture</p>
                        <p class="app-description">(tables, sofas, and chairs, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/012-conference.svg') }}">
                        </div>
                        <p class="app-name">Conferences and Special 
                            Events Supplies</p>
                        <p class="app-description">(conferences and weddings halls, large screen displays and sound systems, tables chairs and rugs, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/013-clothes-hanger.svg') }}">
                        </div>
                        <p class="app-name">Clothing and Accessories for 
                            Special Events</p>
                        <p class="app-description">(women-dresses, Arabian gowns for men, formal jackets, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/014-hair-dryer.svg') }}">
                        </div>
                        <p class="app-name">Beauty Devices and Supplies</p>
                        <p class="app-description">(hair drier, home laser machine, makeup case, and more).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/015-boat.svg') }}">
                        </div>
                        <p class="app-name">Outdoor Journeys and Supplies</p>
                        <p class="app-description">(camps, air blower, electric motor, boats, and motorbikes, and more).</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection