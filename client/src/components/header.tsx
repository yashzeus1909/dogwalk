import { Menu } from "lucide-react";
import { Button } from "@/components/ui/button";
import logoImage from "@assets/gold bone _1749526479486.jpeg";

export default function Header() {
  return (
    <header className="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-100">
      <div className="px-4 py-3">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <div className="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden">
              <img 
                src={logoImage} 
                alt="PawWalk Logo" 
                className="w-full h-full object-contain"
              />
            </div>
            <h1 className="text-xl font-bold text-neutral-900">PawWalk</h1>
          </div>
          <Button 
            variant="ghost" 
            size="icon"
            className="text-neutral-500 hover:text-neutral-900"
          >
            <Menu className="w-6 h-6" />
          </Button>
        </div>
      </div>
    </header>
  );
}
