

<div class="mb-4 flex justify-between items-center">

    <div class="text-2xl text-secondary">{{ $location }}</div>

    <div class="w-44">
        <x-elements.select
        data-label="Checklist Type"
        :size="'md'"
        @change="active_type = $event.currentTarget.value">
            <option value="listing">Listing</option>
            <option value="contract">Contract/Lease</option>
        </x-elements.select>
    </div>

    <x-elements.button
        class="mr-4"
        :buttonClass="'primary'"
        :buttonSize="'md'"
        type="button">
        <i class="fad fa-clone mr-2"></i> Copy Checklists
    </x-elements.button>

</div>

<div class="">

    @foreach($property_types as $property_type)

        @php
        $property_type_id = $property_type -> id;
        $checklists = $property_type -> checklists;
        @endphp

        <div class="my-5 border p-4 mr-2 rounded">

            <div class="flex justify-start items-center pb-4 mb-4 border-b">

                <x-elements.button
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    type="button"
                    @click="add_edit_checklist($event.currentTarget, 'add', '', location_id, '', '{{ $property_type_id }}', '',  '', '')">
                        <i class="fal fa-plus mr-2"></i> Add
                </x-elements.button>

                <div class="text-xl ml-4">{{ $property_type -> property_type }}</div>

            </div>

            <div class="checklist-sortable">
                @foreach($checklists as $checklist)

                    @php
                    $property_type = $checklist -> property_type -> property_type;
                    $property_sub_type = null;
                    if($checklist -> property_sub_type) {
                        $property_sub_type = $checklist -> property_sub_type -> property_sub_type;
                    }
                    $items = $checklist -> items;
                    $checklist_id = $checklist -> id;
                    $sale_rent = $checklist -> checklist_sale_rent;
                    $property_type_id = $checklist -> checklist_property_type_id;
                    $property_sub_type_id = $checklist -> checklist_property_sub_type_id;
                    $checklist_type = $checklist -> checklist_type;
                    $represent = $checklist -> checklist_represent;
                    @endphp

                    <div class="checklist flex justify-between items-center my-3 pb-2 border-b text-sm">

                        <div class="flex justify-start items-center">

                            <div class="mr-3 handle cursor-pointer w-12">
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

                                <x-elements.button
                                    class="ml-3"
                                    :buttonClass="'primary'"
                                    :buttonSize="'sm'"
                                    type="button"
                                    @click="add_items('{{ $checklist_id }}')">
                                    <i class="fal fa-plus mr-2"></i> Add Items
                                </x-elements.button>

                                <x-elements.button
                                    class="ml-3"
                                    :buttonClass="'primary'"
                                    :buttonSize="'sm'"
                                    type="button"
                                    @click="add_edit_checklist($event.currentTarget, 'edit', '{{ $checklist_id }}', location_id, '{{ $sale_rent }}', '{{ $property_type_id }}', '{{ $property_sub_type_id }}', '{{ $checklist_type }}', '{{ $represent }}')">
                                    <i class="fal fa-edit mr-2"></i> Edit
                                </x-elements.button>


                                <x-elements.button
                                    class="ml-3"
                                    :buttonClass="'danger'"
                                    :buttonSize="'sm'"
                                    type="button"
                                    @click="delete_checklist('{{ $checklist_id }}')">
                                    <i class="fal fa-trash mr-2"></i> Delete
                                </x-elements.button>

                                <x-elements.button
                                    class="ml-3"
                                    :buttonClass="'primary'"
                                    :buttonSize="'sm'"
                                    type="button"
                                    @click="add_edit_checklist($event.currentTarget, 'add', '', location_id, '{{ $sale_rent }}', '{{ $property_type_id }}', '{{ $property_sub_type_id }}', '{{ $checklist_type }}', '{{ $represent }}')">
                                    <i class="fal fa-clone mr-2"></i> Duplicate
                                </x-elements.button>

                        </div>

                    </div>

                @endforeach
            </div>

        </div>

    @endforeach

</div>
