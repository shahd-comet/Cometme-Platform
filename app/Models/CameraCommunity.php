<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use App\Models\Compound;

    class CameraCommunity extends Model
    {
        use HasFactory;

        protected $fillable = ['community_id', 'household_id', 'repository_id'];


        public function Community()
        {
            
            return $this->belongsTo(Community::class, 'community_id', 'id');
        }

        public function Household()
        {
            
            return $this->belongsTo(Household::class, 'household_id', 'id');
        }

        public function Repository()
        {
            
            return $this->belongsTo(Repository::class, 'repository_id', 'id');
        }

        public function compound()
        {
            return $this->belongsTo(Compound::class, 'compound_id', 'id');
        }

        public function cameraCommunityTypes()
        {
            return $this->hasMany(CameraCommunityType::class, 'camera_community_id');
        }

        public function getTotalCamerasAttribute()
        {
            return $this->cameraCommunityTypes->sum('number');
        }

        public function getDisplayNameAttribute()
        {
            $community = $this->community ?? null;
            $compound = $this->compound ?? null;
            $repository = $this->repository ?? null;
            $household = $this->household ?? null;

            $communityName = $community ? ($community->english_name ?: $community->arabic_name ?: null) : null;
            $repositoryName = $repository ? ($repository->name ?: null) : null;
            $compoundName = $compound ? ($compound->english_name ?: null) : null;
            $householdName = $household ? ($household->english_name ?: null) : null;

            // If both community and compound exist, prefer "Community - Compound",
            // but avoid duplication when the two names are identical.
            if ($communityName && $compoundName) {
                $c = trim($communityName);
                $p = trim($compoundName);
                if ($c === $p) {
                    return $c;
                }
                return $c . ' - ' . $p;
            }

            // Otherwise, if an explicit display_name was set, use it
            if (!empty($this->attributes['display_name'])) {
                return $this->attributes['display_name'];
            }

            // Build a base from community and repository, removing duplicates
            $parts = array_filter([$communityName, $repositoryName]);
            $parts = array_values(array_unique(array_map('trim', $parts)));

            if (!empty($parts)) {
                $base = implode(' / ', $parts);
                // If compound equals base or is already included, avoid repeating
                if ($compoundName && in_array(trim($compoundName), $parts, true)) {
                    return $base;
                }
                return $compoundName ? ($base . ' - ' . $compoundName) : $base;
            }

            if ($householdName) return $householdName;
            if ($compoundName) return $compoundName;
            if ($repositoryName) return $repositoryName;

            return 'Unknown (cc:'.$this->id.' c:'.$this->community_id.' comp:'.$this->compound_id.' repo:'.$this->repository_id.' hh:'.($this->household_id ?? 'NULL').')';
        }
    }
