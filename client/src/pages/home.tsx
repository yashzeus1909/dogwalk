import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import type { Walker } from "@shared/schema";
import Header from "@/components/header";
import SearchBar from "@/components/search-bar";
import WalkerCard from "@/components/walker-card";
import BookingModal from "@/components/booking-modal";
import BottomNavigation from "@/components/bottom-navigation";
import { Button } from "@/components/ui/button";

export default function Home() {
  const [selectedWalker, setSelectedWalker] = useState<Walker | null>(null);
  const [isBookingModalOpen, setIsBookingModalOpen] = useState(false);
  const [searchLocation, setSearchLocation] = useState("");
  const [selectedService, setSelectedService] = useState("Walking");

  const { data: walkers = [], isLoading } = useQuery<Walker[]>({
    queryKey: ['/api/walkers'],
  });

  const handleBookWalker = (walker: Walker) => {
    setSelectedWalker(walker);
    setIsBookingModalOpen(true);
  };

  const closeBookingModal = () => {
    setIsBookingModalOpen(false);
    setSelectedWalker(null);
  };

  const filteredWalkers = walkers.filter((walker) => {
    // Filter by service type
    const serviceMapping = {
      "Walking": "Dog Walking",
      "Pet Sitting": "Pet Sitting", 
      "Overnight": "Overnight Care",
      "Grooming": "Grooming"
    };
    
    const searchService = serviceMapping[selectedService as keyof typeof serviceMapping] || selectedService;
    
    // Check if walker offers the selected service
    let hasService = false;
    if (walker.badges && Array.isArray(walker.badges)) {
      // Check badges for service keywords
      hasService = walker.badges.some(badge => 
        badge.toLowerCase().includes(selectedService.toLowerCase())
      );
    }
    
    // Also check description for service keywords
    if (!hasService && walker.description) {
      const descLower = walker.description.toLowerCase();
      const serviceLower = selectedService.toLowerCase();
      hasService = descLower.includes(serviceLower) || 
                  descLower.includes(searchService.toLowerCase()) ||
                  (serviceLower === "walking" && descLower.includes("walk")) ||
                  (serviceLower === "pet sitting" && descLower.includes("sit")) ||
                  (serviceLower === "grooming" && descLower.includes("groom"));
    }
    
    // Default to showing all walkers for "Walking" service
    if (!hasService && selectedService === "Walking") {
      hasService = true;
    }
    
    // Filter by location if provided
    const hasLocation = !searchLocation || 
                       walker.distance?.toLowerCase().includes(searchLocation.toLowerCase()) ||
                       walker.availability?.toLowerCase().includes(searchLocation.toLowerCase());
    
    return hasService && hasLocation;
  });

  return (
    <div className="min-h-screen bg-neutral-50">
      <Header />
      <SearchBar 
        searchLocation={searchLocation}
        onLocationChange={setSearchLocation}
        selectedService={selectedService}
        onServiceChange={setSelectedService}
      />
      
      <main className="px-4 py-6 space-y-4">
        {isLoading ? (
          <div className="space-y-4">
            {[...Array(3)].map((_, i) => (
              <div key={i} className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div className="animate-pulse">
                  <div className="flex space-x-4">
                    <div className="w-16 h-16 bg-gray-200 rounded-full"></div>
                    <div className="flex-1 space-y-2">
                      <div className="h-4 bg-gray-200 rounded w-1/4"></div>
                      <div className="h-3 bg-gray-200 rounded w-1/3"></div>
                      <div className="h-3 bg-gray-200 rounded w-full"></div>
                      <div className="h-3 bg-gray-200 rounded w-2/3"></div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <>
            {filteredWalkers.map((walker) => (
              <WalkerCard
                key={walker.id}
                walker={walker}
                onBook={() => handleBookWalker(walker)}
              />
            ))}
            
            <div className="text-center py-8">
              <Button 
                variant="outline" 
                className="px-6 py-3"
              >
                Load More Walkers
              </Button>
            </div>
          </>
        )}
      </main>

      <BookingModal
        walker={selectedWalker}
        isOpen={isBookingModalOpen}
        onClose={closeBookingModal}
      />

      <BottomNavigation />
    </div>
  );
}
