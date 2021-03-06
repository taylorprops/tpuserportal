<div class="checklist-container">

    <div class="mb-4 flex justify-between items-center">

        <div class="text-2xl text-secondary">{{ $location }}</div>

        <div class="w-44">
            <select class="form-element select md" data-label="Checklist Type" @change="filter_checklists($event.currentTarget)">
                <option value="listing">Listing</option>
                <option value="contract">Contract/Lease</option>
            </select>
        </div>

        <button type="button" class="button primary md mr-4" <i class="fad fa-clone mr-2"></i> Copy Checklists
        </button>

    </div>

    <div>

        @foreach ($property_types as $property_type)
            {{-- blade-formatter-disable --}}
            @php
                $property_type_id = $property_type -> id;
                $checklists = $property_type -> checklists;
            @endphp
{{-- blade-formatter-enable --}}

            <div class="my-5 border p-4 mr-2 rounded">

                <div class="flex justify-start items-center pb-4 mb-4 border-b">

                    <button type="button" class="button primary md"
                        @click="add_edit_checklist($event.currentTarget, 'add', '', location_id, '', '{{ $property_type_id }}', '',  '', '')">
                        <i class="fal fa-plus mr-2"></i> Add
                    </button>

                    <div class="text-xl ml-4">{{ $property_type -> property_type }}</div>

                </div>

                <div class="checklist-sortable">
                    @foreach ($checklists as $checklist)
                        {{-- blade-formatter-disable --}}
                        @php
                            $property_type = $checklist -> property_type -> property_type;
                            $property_sub_type = null;
                            if ($checklist -> property_sub_type) {
                                $property_sub_type = $checklist -> property_sub_type -> property_sub_type;
                            }
                            $items = $checklist -> items;
                            $checklist_id = $checklist -> id;
                            $sale_rent = $checklist -> checklist_sale_rent;
                            $property_type_id = $checklist -> checklist_property_type_id;
                            $property_sub_type_id = $checklist -> checklist_property_sub_type_id;
                            $checklist_type = $checklist -> checklist_type;
                            $represent = $checklist -> checklist_represent;
                            $location = $checklist -> checklist_state.' : '.$checklist -> location -> location;
                        @endphp
{{-- blade-formatter-enable --}}

                        <div class="checklist justify-between items-center my-3 pb-2 border-b text-sm
                        @if ($checklist_type == 'listing') flex @else hidden @endif"
                            data-checklist-id="{{ $checklist_id }}">

                            <div class="flex justify-start items-center">

                                <div class="mr-3 checklist-handle cursor-pointer w-12">
                                    <i class="fa fa-bars fa-lg"></i>
                                </div>

                                <div class="ml-2 mr-3 w-16">
                                    {{ ucwords($sale_rent) }}
                                </div>

                                <div class="mr-3 w-24">
                                    {{ $represent == 'seller' ? 'Seller/Owner' : 'Buyer/Renter' }}
                                </div>

                                <div class="mr-3">
                                    {{ $property_sub_type }}
                                </div>

                            </div>

                            <div class="flex justify-end items-center">

                                <div class="px-2 py-1 rounded bg-primary-lightest text-primary-dark text-center">{{ count($items) }}</div>

                                <button type="button" class="button primary sm ml-3"
                                    @click="add_items(`{{ $checklist_id }}`, `{{ $property_type }}`, `{{ $property_sub_type }}`, `{{ $checklist_type }}`, `{{ $sale_rent }}`, `{{ $represent }}`, `{{ $location }}`)">
                                    <i class="fal fa-plus mr-2"></i> Add Items
                                </button>

                                <button type="button" class="button primary sm ml-3"
                                    @click="add_edit_checklist($event.currentTarget, `edit`, `{{ $checklist_id }}`, location_id, `{{ $sale_rent }}`, `{{ $property_type_id }}`, `{{ $property_sub_type_id }}`, `{{ $checklist_type }}`, `{{ $represent }}`)">
                                    <i class="fal fa-edit mr-2"></i> Edit
                                </button>


                                <button type="button" class="button danger sm ml-3" @click="delete_checklist(`{{ $checklist_id }}`)">
                                    <i class="fal fa-trash mr-2"></i> Delete
                                </button>

                                <button type="button" class="button primary sm ml-3"
                                    @click="add_edit_checklist($event.currentTarget, 'add', '', location_id, '{{ $sale_rent }}', '{{ $property_type_id }}', '{{ $property_sub_type_id }}', '{{ $checklist_type }}', '{{ $represent }}')">
                                    <i class="fal fa-clone mr-2"></i> Duplicate
                                </button>

                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach

    </div>

</div>
