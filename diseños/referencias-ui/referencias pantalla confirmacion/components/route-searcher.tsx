"use client"

import { useState } from "react"
import { ChevronLeft, Search, ChevronDown, Calendar, FileText, Map, List } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Card } from "@/components/ui/card"
import { cn } from "@/lib/utils"

interface Route {
  id: string
  name: string
  startTime: string
  endTime: string
}

const routes: Route[] = [
  { id: "1", name: "Route AB", startTime: "09:00", endTime: "09:30" },
  { id: "2", name: "Route CB", startTime: "09:00", endTime: "09:30" },
  { id: "3", name: "Route DC", startTime: "09:00", endTime: "09:30" },
  { id: "4", name: "Route EF", startTime: "10:00", endTime: "10:45" },
]

export function RouteSearcher() {
  const [viewMode, setViewMode] = useState<"list" | "map">("list")
  const [filtersOpen, setFiltersOpen] = useState(false)
  const [currentStep] = useState(1)

  return (
    <div className="relative min-h-screen w-full max-w-md mx-auto bg-background overflow-hidden">
      {/* Map Background (visible only in map mode) */}
      {viewMode === "map" && (
        <div className="absolute inset-0 z-0">
          <iframe
            src="https://www.openstreetmap.org/export/embed.html?bbox=-46.6600%2C-23.5700%2C-46.6400%2C-23.5500&amp;layer=mapnik&amp;marker=-23.5617%2C-46.6553"
            className="w-full h-full border-0"
            title="Mapa de ubicación"
          />
          {/* You are here marker overlay */}
          <div className="absolute bottom-1/3 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center">
            <div className="w-4 h-4 rounded-full bg-white border-2 border-red-500 shadow-lg" />
            <div className="mt-1 bg-white px-3 py-1 rounded-full shadow-md text-sm font-medium text-foreground">
              You are here
            </div>
          </div>
        </div>
      )}

      {/* Header */}
      <header className="relative z-10 bg-accent text-accent-foreground px-4 py-3">
        {/* Status bar simulation */}
        <div className="flex items-center justify-between text-sm mb-2">
          <span className="font-medium">9:41</span>
          <div className="flex items-center gap-1">
            <div className="flex gap-0.5">
              <div className="w-1 h-2 bg-accent-foreground rounded-sm" />
              <div className="w-1 h-2.5 bg-accent-foreground rounded-sm" />
              <div className="w-1 h-3 bg-accent-foreground rounded-sm" />
              <div className="w-1 h-3.5 bg-accent-foreground rounded-sm" />
            </div>
            <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 3C7.46 3 3.34 5.78 1.18 10l1.64 1.18C4.56 7.9 8.12 5.5 12 5.5s7.44 2.4 9.18 5.68L22.82 10C20.66 5.78 16.54 3 12 3zm0 5c-2.7 0-5.13 1.38-6.58 3.5L7 12.68C8.08 11.06 9.94 10 12 10s3.92 1.06 5 2.68l1.58-1.18C17.13 9.38 14.7 8 12 8z" />
            </svg>
            <div className="w-6 h-3 bg-accent-foreground rounded-sm relative">
              <div className="absolute right-0.5 top-0.5 bottom-0.5 w-1 bg-accent rounded-sm" />
            </div>
          </div>
        </div>

        {/* Navigation */}
        <div className="flex items-center justify-between">
          <button className="p-1 -ml-1">
            <ChevronLeft className="w-6 h-6" />
          </button>
          <h1 className="text-lg font-semibold text-primary">Booking</h1>
          <div className="w-6" />
        </div>
      </header>

      {/* Search and Steps */}
      <div className={cn(
        "relative z-10 px-4 py-3 flex items-center justify-between gap-4",
        viewMode === "map" ? "bg-transparent" : "bg-background"
      )}>
        <button className="w-10 h-10 rounded-full bg-card shadow-md flex items-center justify-center">
          <Search className="w-5 h-5 text-muted-foreground" />
        </button>

        {/* Steps indicator */}
        <div className="flex items-center gap-0">
          {[1, 2, 3].map((step, index) => (
            <div key={step} className="flex items-center">
              <div
                className={cn(
                  "w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium border-2",
                  step === currentStep
                    ? "bg-primary text-primary-foreground border-primary"
                    : step < currentStep
                      ? "bg-primary/20 text-primary border-primary/20"
                      : "bg-card text-muted-foreground border-border"
                )}
              >
                {step}
              </div>
              {index < 2 && (
                <div
                  className={cn(
                    "w-8 h-0.5",
                    step < currentStep ? "bg-primary" : "bg-border"
                  )}
                />
              )}
            </div>
          ))}
        </div>
      </div>

      {/* View Toggle Button */}
      <div className={cn(
        "relative z-10 px-4 pb-2",
        viewMode === "map" ? "bg-transparent" : "bg-background"
      )}>
        <div className="flex justify-end">
          <div className="inline-flex rounded-full bg-card shadow-md p-1">
            <button
              onClick={() => setViewMode("list")}
              className={cn(
                "flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all",
                viewMode === "list"
                  ? "bg-primary text-primary-foreground"
                  : "text-muted-foreground hover:text-foreground"
              )}
            >
              <List className="w-4 h-4" />
              Lista
            </button>
            <button
              onClick={() => setViewMode("map")}
              className={cn(
                "flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all",
                viewMode === "map"
                  ? "bg-primary text-primary-foreground"
                  : "text-muted-foreground hover:text-foreground"
              )}
            >
              <Map className="w-4 h-4" />
              Mapa
            </button>
          </div>
        </div>
      </div>

      {/* Filters */}
      <div className={cn(
        "relative z-10 px-4 py-2",
        viewMode === "map" ? "bg-transparent" : "bg-background"
      )}>
        <button
          onClick={() => setFiltersOpen(!filtersOpen)}
          className="w-full bg-card rounded-2xl shadow-sm px-5 py-3.5 flex items-center justify-between"
        >
          <span className="text-lg font-medium text-foreground">Filters</span>
          <ChevronDown
            className={cn(
              "w-5 h-5 text-muted-foreground transition-transform",
              filtersOpen && "rotate-180"
            )}
          />
        </button>
      </div>

      {/* Content Area */}
      <div className={cn(
        "relative z-10 px-4 py-4",
        viewMode === "map" ? "bg-transparent" : "bg-background"
      )}>
        {viewMode === "list" ? (
          <>
            <h2 className="text-lg font-semibold text-center text-foreground mb-4">
              Available routes
            </h2>

            <div className="space-y-3">
              {routes.map((route) => (
                <Card
                  key={route.id}
                  className="p-4 rounded-2xl shadow-sm border-0 bg-card"
                >
                  <h3 className="text-lg font-semibold text-primary mb-1">
                    {route.name}
                  </h3>
                  <p className="text-base text-muted-foreground mb-3">
                    {route.startTime} → {route.endTime}
                  </p>
                  <div className="flex gap-2">
                    <Button
                      variant="outline"
                      className="flex-1 rounded-full border-border hover:bg-muted"
                    >
                      <Calendar className="w-4 h-4 mr-2" />
                      Disponibilidad
                    </Button>
                    <Button
                      variant="outline"
                      className="flex-1 rounded-full border-border hover:bg-muted"
                    >
                      <FileText className="w-4 h-4 mr-2" />
                      Ver detalles
                    </Button>
                  </div>
                </Card>
              ))}
            </div>
          </>
        ) : (
          <div className="space-y-3 mt-auto">
            {/* In map view, show floating route cards at the bottom */}
            <div className="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-4 pointer-events-none">
              <div className="pointer-events-auto">
                <Card className="p-4 rounded-2xl shadow-lg border-0 bg-card/95 backdrop-blur-sm">
                  <h3 className="text-lg font-semibold text-primary mb-1">
                    Route AB
                  </h3>
                  <p className="text-sm text-muted-foreground mb-3">
                    09:00 → 09:30 • Nearest route
                  </p>
                  <div className="flex gap-2">
                    <Button
                      variant="outline"
                      className="flex-1 rounded-full border-border hover:bg-muted text-sm"
                    >
                      <Calendar className="w-4 h-4 mr-1.5" />
                      Disponibilidad
                    </Button>
                    <Button className="flex-1 rounded-full bg-primary text-primary-foreground text-sm">
                      Seleccionar
                    </Button>
                  </div>
                </Card>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Bottom padding for list view */}
      {viewMode === "list" && <div className="h-8" />}
    </div>
  )
}
