<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 
    'property_id', 
    'name', 
    'phone', 
    'email', 
    'date', 
    'time', 
    'message', 
    'status',
    'reject_reason'
])]
class Appointment extends Model
{
    /**
     * Get the user who scheduled the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property scheduled for viewing.
     */
    public function property()
    {
        $instance = $this->newRelatedInstance(Property::class);
        return new \App\Relations\SafeUuidBelongsTo(
            $instance->newQuery(), $this, 'property_id', 'id', 'property'
        );
    }

    /**
     * Accessor to dynamically resolve database or NKS API properties.
     */
    public function getPropertyAttribute()
    {
        $property = $this->getRelationValue('property');
        if ($property) {
            return $property;
        }

        if ($this->property_id) {
            try {
                if (\Illuminate\Support\Str::isUuid($this->property_id)) {
                    $dbProperty = Property::find($this->property_id);
                    if ($dbProperty) {
                        return $dbProperty;
                    }
                }
                
                $propertyData = app(\App\Services\PropertyService::class)->getPropertyById($this->property_id);
                if ($propertyData) {
                    $virtualProperty = new Property();
                    $virtualProperty->id = $propertyData['id'];
                    $virtualProperty->title = $propertyData['title'];
                    $virtualProperty->price = $propertyData['price_raw'];
                    $virtualProperty->location = $propertyData['location'];
                    $virtualProperty->address = $propertyData['location'];
                    $virtualProperty->ward = '';
                    $virtualProperty->district = $propertyData['district'];
                    $virtualProperty->city = 'Thành phố Hồ Chí Minh';
                    
                    // Mock agent/owner object
                    $agentObj = new \stdClass();
                    $agentObj->name = $propertyData['agent']['name'] ?? 'Môi giới';
                    $agentObj->phone = $propertyData['agent']['phone'] ?? '';
                    $agentObj->email = '';
                    
                    $virtualProperty->setRelation('agent', $agentObj);
                    $virtualProperty->setRelation('owner', $agentObj);
                    
                    return $virtualProperty;
                }
            } catch (\Exception $e) {
                // Return null on failure
            }
        }

        return null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
