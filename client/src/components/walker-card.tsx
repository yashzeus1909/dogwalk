import { Star, Clock, CheckCircle, Edit } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Link } from "wouter";
import type { Walker } from "@shared/schema";

interface WalkerCardProps {
  walker: Walker;
  onBook: () => void;
}

export default function WalkerCard({ walker, onBook }: WalkerCardProps) {
  const renderStars = (rating: number) => {
    const fullStars = Math.floor(rating / 10);
    const hasHalfStar = (rating % 10) >= 5;
    const stars = [];
    
    for (let i = 0; i < 5; i++) {
      if (i < fullStars) {
        stars.push(
          <Star key={i} className="w-4 h-4 fill-current text-amber-400" />
        );
      } else if (i === fullStars && hasHalfStar) {
        stars.push(
          <Star key={i} className="w-4 h-4 fill-current text-amber-400" />
        );
      } else {
        stars.push(
          <Star key={i} className="w-4 h-4 text-neutral-300 fill-current" />
        );
      }
    }
    
    return stars;
  };

  const formatRating = (rating: number) => {
    return (rating / 10).toFixed(1);
  };

  const getBadgeText = () => {
    if (walker.certified) return "Certified trainer";
    if (walker.backgroundCheck) return "Background checked";
    if (walker.insured) return "Insured";
    
    // Handle badges array from unknown type
    let badges = [];
    if (typeof walker.badges === 'string') {
      try {
        badges = JSON.parse(walker.badges);
      } catch {
        badges = [];
      }
    } else if (Array.isArray(walker.badges)) {
      badges = walker.badges;
    }
    
    return badges[0] || "";
  };

  return (
    <Card className="overflow-hidden shadow-sm border border-gray-100">
      <CardContent className="p-4">
        <div className="flex space-x-4">
          <img 
            src={walker.image || "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=300&h=300&fit=crop&crop=face"} 
            alt={`${walker.name} - Dog Walker`}
            className="w-16 h-16 rounded-full object-cover flex-shrink-0"
          />
          <div className="flex-1 min-w-0">
            <div className="flex items-start justify-between">
              <div>
                <h3 className="font-semibold text-neutral-900 text-lg">
                  {walker.name}
                </h3>
                <div className="flex items-center space-x-1 mt-1">
                  <div className="flex">
                    {renderStars(walker.rating)}
                  </div>
                  <span className="text-sm text-neutral-600">
                    {formatRating(walker.rating)} ({walker.reviewCount} reviews)
                  </span>
                </div>
                <p className="text-sm text-neutral-600 mt-1">
                  {walker.distance}
                </p>
              </div>
              <div className="text-right">
                <div className="text-lg font-semibold text-neutral-900">
                  ${walker.price}
                </div>
                <div className="text-sm text-neutral-500">per walk</div>
              </div>
            </div>
            
            <p className="text-sm text-neutral-700 mt-3 line-clamp-2">
              {walker.description}
            </p>
            
            <div className="flex items-center justify-between mt-4">
              <div className="flex items-center space-x-4 text-sm text-neutral-600">
                <div className="flex items-center space-x-1">
                  <Clock className="w-4 h-4" />
                  <span>{walker.availability}</span>
                </div>
                <div className="flex items-center space-x-1">
                  <CheckCircle className="w-4 h-4" />
                  <span>{getBadgeText()}</span>
                </div>
              </div>
              <div className="flex gap-2">
                <Link href={`/edit-walker/${walker.id}`}>
                  <Button 
                    variant="outline"
                    size="sm"
                    className="px-3 py-1"
                  >
                    <Edit className="w-4 h-4" />
                  </Button>
                </Link>
                <Button 
                  onClick={onBook}
                  className="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90"
                >
                  Book Now
                </Button>
              </div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
