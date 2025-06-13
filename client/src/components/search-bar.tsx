import { MapPin } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

interface SearchBarProps {
  searchLocation: string;
  onLocationChange: (location: string) => void;
  selectedService: string;
  onServiceChange: (service: string) => void;
}

export default function SearchBar({ 
  searchLocation, 
  onLocationChange, 
  selectedService, 
  onServiceChange 
}: SearchBarProps) {
  const services = ["All Services", "Dog Walking", "Pet Sitting", "Pet Boarding", "Doggy Daycare", "Grooming"];

  return (
    <div className="px-4 py-4 bg-white border-b border-gray-100">
      <div className="space-y-3">
        <div className="relative">
          <MapPin className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-500" />
          <Input 
            type="text" 
            placeholder="Enter your location" 
            value={searchLocation}
            onChange={(e) => onLocationChange(e.target.value)}
            className="pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
          />
        </div>
        <div className="flex space-x-2 overflow-x-auto pb-1">
          {services.map((service) => (
            <Button
              key={service}
              variant={selectedService === service ? "default" : "secondary"}
              size="sm"
              onClick={() => onServiceChange(service)}
              className={`whitespace-nowrap rounded-full ${
                selectedService === service 
                  ? "bg-primary text-white hover:bg-primary/90" 
                  : "bg-neutral-100 text-neutral-700 hover:bg-neutral-200"
              }`}
            >
              {service}
            </Button>
          ))}
        </div>
      </div>
    </div>
  );
}
